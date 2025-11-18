<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Unit;
use App\Models\ProductUnitConversion;
use App\Models\Inventory;

class UnitConversionService
{
    /**
     * Convertir cantidad de una unidad a la unidad base del producto
     */
    public function convertToBaseUnit($productId, $quantity, $unitCode)
    {
        $conversion = ProductUnitConversion::getConversionByUnitCode($productId, $unitCode);
        $product = Product::find($productId);

        if (!$conversion) {
            // Si no hay conversión, devolver la cantidad original (sin conversión)
            return $quantity;
        }

        // Para productos por volumen, la conversión es inversa
        if ($product && $product->sale_type === 'volume') {
            $baseQuantity = $quantity / $conversion->conversion_factor;
        } else {
            $baseQuantity = $quantity * $conversion->conversion_factor;
        }

        return [
            'base_quantity' => $baseQuantity,
            'conversion_factor' => $conversion->conversion_factor,
            'unit_id' => $conversion->unit_id,
            'unit_name' => $conversion->unit->unit_name
        ];
    }

    /**
     * Convertir cantidad de unidad base a una unidad específica
     */
    public function convertFromBaseUnit($productId, $baseQuantity, $unitCode)
    {
        $conversion = ProductUnitConversion::getConversionByUnitCode($productId, $unitCode);
        $product = Product::find($productId);

        if (!$conversion) {
            // Si no hay conversión, devolver la cantidad base original (sin conversión)
            return $baseQuantity;
        }

        // Para productos por volumen, la conversión es inversa
        if ($product && $product->sale_type === 'volume') {
            $quantity = $baseQuantity * $conversion->conversion_factor;
        } else {
            $quantity = $baseQuantity / $conversion->conversion_factor;
        }

        return [
            'quantity' => $quantity,
            'conversion_factor' => $conversion->conversion_factor,
            'unit_id' => $conversion->unit_id,
            'unit_name' => $conversion->unit->unit_name
        ];
    }

    /**
     * Calcular precio por unidad específica usando información de peso real
     */
    public function calculateUnitPrice($productId, $basePrice, $unitCode)
    {
        $product = Product::find($productId);
        $conversion = ProductUnitConversion::getConversionByUnitCode($productId, $unitCode);

        // Si no hay conversión configurada, usar lógica por defecto
        if (!$conversion) {
            return $this->calculateDefaultUnitPrice($product, $unitCode, $basePrice);
        }

        // Si el producto es por peso
        if ($product && $product->sale_type === 'weight' && $product->weight_per_unit) {
            $pricePerPound = $product->getPricePerPound();

            // Calcular precio basado en el peso real
            switch ($unitCode) {
                case '36': // Libra
                    return $pricePerPound; // Precio por libra

                case '59': // Unidad (Saco completo)
                    return $product->price; // Precio por saco completo

                case '34': // Kilogramo
                    return $pricePerPound * 2.2046; // Precio por kg

                case '99': // Dólar (valor monetario)
                    // Para productos por peso, el dólar representa el valor monetario
                    // Por ejemplo, si el saco cuesta $42.50, entonces $1 = $1.00
                    return 1.0;

                default:
                    // Usar el método original como fallback
                    return $basePrice * $conversion->conversion_factor * $conversion->price_multiplier;
            }
        }

        // Si el producto es por volumen
        if ($product && $product->sale_type === 'volume' && $product->volume_per_unit) {
            $pricePerLiter = $product->getPricePerLiter();

            switch ($unitCode) {
                case '23': // Litro
                    return $pricePerLiter;

                case '59': // Unidad (Galón, etc.)
                    return $product->price; // Precio por unidad completa

                case '26': // Mililitro
                    return $pricePerLiter / 1000;

                case '99': // Dólar
                    return 1.0;

                default:
                    return $basePrice * $conversion->conversion_factor * $conversion->price_multiplier;
            }
        }

        // Si el producto es por unidad (count)
        if ($product && $product->sale_type === 'unit') {
            switch ($unitCode) {
                case '59': // Unidad
                    return $product->price; // Precio por unidad

                case '36': // Libra (si se vende por peso)
                    // Si el producto tiene peso por unidad, calcular precio por libra
                    if ($product->weight_per_unit) {
                        return $product->getPricePerPound();
                    }
                    return $product->price; // Fallback al precio por unidad

                case '99': // Dólar
                    return 1.0;

                default:
                    return $product->price; // Precio por unidad por defecto
            }
        }

        // Método original como fallback
        return $basePrice * $conversion->conversion_factor * $conversion->price_multiplier;
    }

    /**
     * Calcular conversión completa para ventas con descuento de inventario
     * Esta función maneja la lógica específica para productos agropecuarios
     */
    public function calculateSaleConversion($productId, $quantity, $unitCode)
    {
        $product = Product::find($productId);

        if (!$product) {
            // Si no se encuentra el producto, devolver información básica sin conversión
            return [
                'success' => false,
                'error' => 'Producto no encontrado',
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_code' => $unitCode,
                'conversion_applied' => false,
                'base_quantity' => $quantity,
                'available_stock' => 0,
                'sufficient_stock' => false
            ];
        }

        // Obtener información del inventario
        $inventory = $product->inventory;
        $availableStock = $inventory ? $inventory->base_quantity : 0;
        $inventoryBaseUnitId = $inventory ? $inventory->base_unit_id : null;

        // Calcular precio unitario
        $unitPrice = $this->calculateUnitPrice($productId, $product->price, $unitCode);

        // Calcular subtotal
        $subtotal = $quantity * $unitPrice;

        // Calcular cantidad base necesaria para el inventario
        $baseQuantityNeeded = $this->calculateBaseQuantityNeeded($productId, $quantity, $unitCode);

        // Verificar disponibilidad de stock
        $stockAvailable = $availableStock >= $baseQuantityNeeded;

        // Determinar información de stock según la unidad base del inventario
        $stockInfo = [
            'available_quantity' => $availableStock,
            'available' => $stockAvailable,
            'remaining_after_sale' => $availableStock - $baseQuantityNeeded
        ];

        // Determinar información de stock según el tipo de venta del producto
        if ($product->sale_type === 'weight') {
            // Producto por peso
            if ($inventoryBaseUnitId == 28) { // Inventario en sacos
                $stockInfo['base_unit'] = 'sacos';
                $stockInfo['base_unit_code'] = '59';
                $stockInfo['total_in_lbs'] = $availableStock * $product->weight_per_unit;
                $stockInfo['remaining_in_lbs'] = ($availableStock - $baseQuantityNeeded) * $product->weight_per_unit;
                $stockInfo['measure_type'] = 'weight';
                $stockInfo['measure_unit'] = 'libras';
            } else {
                $stockInfo['base_unit'] = $this->getBaseUnitName($product);
                $stockInfo['base_unit_code'] = $this->getBaseUnitCode($product);
                $stockInfo['total_in_lbs'] = $availableStock;
                $stockInfo['remaining_in_lbs'] = $availableStock - $baseQuantityNeeded;
                $stockInfo['measure_type'] = 'weight';
                $stockInfo['measure_unit'] = 'libras';
            }
        } elseif ($product->sale_type === 'volume') {
            // Producto por volumen
            if ($inventoryBaseUnitId == 28) { // Inventario en depósitos
                $stockInfo['base_unit'] = 'depósitos';
                $stockInfo['base_unit_code'] = '59';
                $stockInfo['total_in_liters'] = $availableStock * $product->volume_per_unit;
                $stockInfo['remaining_in_liters'] = ($availableStock - $baseQuantityNeeded) * $product->volume_per_unit;
                $stockInfo['total_in_ml'] = ($availableStock * $product->volume_per_unit) * 1000;
                $stockInfo['remaining_in_ml'] = (($availableStock - $baseQuantityNeeded) * $product->volume_per_unit) * 1000;
                $stockInfo['measure_type'] = 'volume';
                $stockInfo['measure_unit'] = 'litros';
            } else {
                $stockInfo['base_unit'] = $this->getBaseUnitName($product);
                $stockInfo['base_unit_code'] = $this->getBaseUnitCode($product);
                $stockInfo['total_in_liters'] = $availableStock;
                $stockInfo['remaining_in_liters'] = $availableStock - $baseQuantityNeeded;
                $stockInfo['total_in_ml'] = $availableStock * 1000;
                $stockInfo['remaining_in_ml'] = ($availableStock - $baseQuantityNeeded) * 1000;
                $stockInfo['measure_type'] = 'volume';
                $stockInfo['measure_unit'] = 'litros';
            }
        } else {
            // Producto por unidad
            $stockInfo['base_unit'] = $this->getBaseUnitName($product);
            $stockInfo['base_unit_code'] = $this->getBaseUnitCode($product);
            $stockInfo['measure_type'] = 'unit';
            $stockInfo['measure_unit'] = $this->getBaseUnitName($product);
        }

        return [
            'product_id' => $productId,
            'product_name' => $product->name,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'subtotal' => $subtotal,
            'unit_info' => [
                'unit_code' => $unitCode,
                'unit_name' => $this->getUnitDisplayName($unitCode),
                'unit_id' => $this->getUnitIdByCode($unitCode)
            ],
            'calculations' => [
                'base_quantity_needed' => $baseQuantityNeeded,
                'conversion_factor' => $this->getConversionFactor($productId, $unitCode),
                'base_price' => $product->price,
                'price_per_pound' => $product->getPricePerPound(),
                'weight_per_unit' => $product->weight_per_unit,
                'price_per_sack' => $product->price,
                'price_per_liter' => $product->getPricePerLiter(),
                'volume_per_unit' => $product->volume_per_unit
            ],
            'stock_info' => $stockInfo
        ];
    }

    /**
     * Calcular cantidad base necesaria para una venta
     */
    public function calculateBaseQuantityNeeded($productId, $quantity, $unitCode)
    {
        $product = Product::find($productId);

        if (!$product) {
            return 0;
        }

        // Obtener información del inventario para determinar la unidad base
        $inventory = $product->inventory;
        $inventoryBaseUnitId = $inventory ? $inventory->base_unit_id : null;

        // Para productos por peso
        if ($product->sale_type === 'weight' && $product->weight_per_unit) {
            // Si el inventario está en sacos (unidad), usar sacos como base
            if ($inventoryBaseUnitId == 28) { // ID de Unidad
                switch ($unitCode) {
                    case '36': // Libra
                        return $quantity / $product->weight_per_unit; // Convertir libras a sacos

                    case '59': // Unidad (Saco completo)
                        return $quantity; // 1 saco = 1 saco base

                    case '99': // Dólar
                        return $product->price > 0 ? $quantity / $product->price : 0; // Convertir dólares a sacos

                    default:
                        return $quantity;
                }
            } else {
                // Si el inventario está en libras, usar libras como base (comportamiento original)
                switch ($unitCode) {
                    case '36': // Libra
                        return $quantity; // 1 libra = 1 libra base

                    case '59': // Unidad (Saco completo)
                        return $quantity * $product->weight_per_unit; // Convertir sacos a libras

                    case '99': // Dólar
                        $pricePerPound = $product->getPricePerPound();
                        return $pricePerPound > 0 ? $quantity / $pricePerPound : 0; // Convertir dólares a libras

                    default:
                        return $quantity;
                }
            }
        }

        // Para productos por volumen
        if ($product->sale_type === 'volume' && $product->volume_per_unit) {
            // Si el inventario está en depósitos (unidad), usar depósitos como base
            if ($inventoryBaseUnitId == 28) { // ID de Unidad
                switch ($unitCode) {
                    case '23': // Litro
                        return $quantity / $product->volume_per_unit; // Convertir litros a depósitos

                    case '59': // Unidad (Depósito completo)
                        return $quantity; // 1 depósito = 1 depósito base

                    case '99': // Dólar
                        return $product->price > 0 ? $quantity / $product->price : 0; // Convertir dólares a depósitos

                    default:
                        return $quantity;
                }
            } else {
                // Si el inventario está en litros, usar litros como base
                switch ($unitCode) {
                    case '23': // Litro
                        return $quantity; // 1 litro = 1 litro base

                    case '59': // Unidad (Depósito completo)
                        return $quantity * $product->volume_per_unit; // Convertir depósitos a litros

                    case '99': // Dólar
                        $pricePerLiter = $product->getPricePerLiter();
                        return $pricePerLiter > 0 ? $quantity / $pricePerLiter : 0; // Convertir dólares a litros

                    default:
                        return $quantity;
                }
            }
        }

        // Para productos por unidad
        if ($product->sale_type === 'unit') {
            switch ($unitCode) {
                case '59': // Unidad
                    return $quantity; // 1 unidad = 1 unidad base

                case '36': // Libra (si tiene peso)
                    return $product->weight_per_unit ? $quantity / $product->weight_per_unit : $quantity;

                case '99': // Dólar
                    return $product->price > 0 ? $quantity / $product->price : 0; // Convertir dólares a unidades

                default:
                    return $quantity;
            }
        }

        return $quantity;
    }

    /**
     * Obtener factor de conversión para una unidad
     */
    private function getConversionFactor($productId, $unitCode)
    {
        $conversion = ProductUnitConversion::getConversionByUnitCode($productId, $unitCode);
        return $conversion ? $conversion->conversion_factor : 1.0;
    }

    /**
     * Obtener ID de unidad por código
     */
    private function getUnitIdByCode($unitCode)
    {
        $unit = Unit::where('unit_code', $unitCode)->first();
        return $unit ? $unit->id : null;
    }

    /**
     * Obtener nombre de la unidad base según el tipo de producto
     */
    private function getBaseUnitName($product)
    {
        switch ($product->sale_type) {
            case 'volume':
                return 'litros';
            case 'weight':
                return 'libras';
            case 'unit':
            default:
                return 'unidad';
        }
    }

    /**
     * Obtener código de la unidad base según el tipo de producto
     */
    private function getBaseUnitCode($product)
    {
        switch ($product->sale_type) {
            case 'volume':
                return '23'; // Litro
            case 'weight':
                return '36'; // Libra
            case 'unit':
            default:
                return '59'; // Unidad
        }
    }

    /**
     * Obtener nombre de visualización para unidad
     */
    private function getUnitDisplayName($unitCode)
    {
        $unitNames = [
            '59' => 'Unidad',
            '36' => 'Libra',
            '99' => 'Dólar'
        ];

        return $unitNames[$unitCode] ?? $unitCode;
    }

    /**
     * Obtener información completa de conversión con cálculos detallados
     */
    public function getConversionInfo($productId, $unitCode)
    {
        $product = Product::find($productId);
        $conversion = ProductUnitConversion::getConversionByUnitCode($productId, $unitCode);

        if (!$conversion) {
            // En lugar de lanzar error, devolver información por defecto
            return [
                'conversion_id' => null,
                'unit_id' => null,
                'unit_code' => $unitCode,
                'unit_name' => 'Unidad no configurada',
                'unit_type' => 'unknown',
                'conversion_factor' => 1.0,
                'price_multiplier' => 1.0,
                'is_default' => false,
                'notes' => "No se encontró conversión para la unidad {$unitCode} del producto {$productId}",
                'product_type' => $product ? $product->sale_type : null,
                'unit_description' => "Unidad {$unitCode} sin configuración",
                'price_explanation' => "Precio sin conversión aplicada",
                'warning' => true,
                'warning_message' => "Conversión no configurada para esta unidad"
            ];
        }

        $baseInfo = [
            'conversion_id' => $conversion->id,
            'unit_id' => $conversion->unit_id,
            'unit_code' => $conversion->unit->unit_code,
            'unit_name' => $conversion->unit->unit_name,
            'unit_type' => $conversion->unit->unit_type,
            'conversion_factor' => $conversion->conversion_factor,
            'price_multiplier' => $conversion->price_multiplier,
            'is_default' => $conversion->is_default,
            'notes' => $conversion->notes
        ];

        // Agregar información específica según el tipo de producto
        if ($product) {
            $baseInfo['product_type'] = $product->sale_type;

            // Información de medidas del producto
            if ($product->sale_type === 'weight' && $product->weight_per_unit) {
                $baseInfo['product_measures'] = [
                    'weight_per_unit' => $product->weight_per_unit,
                    'weight_unit' => 'lbs',
                    'price_per_pound' => $product->getPricePerPound(),
                    'total_price' => $product->price
                ];

                // Información específica por unidad
                switch ($unitCode) {
                    case '59': // Unidad (Saco completo)
                        $baseInfo['unit_description'] = "Saco completo de {$product->weight_per_unit} lbs";
                        $baseInfo['price_explanation'] = "Precio por saco completo";
                        break;
                    case '36': // Libra
                        $baseInfo['unit_description'] = "1 libra de producto";
                        $baseInfo['price_explanation'] = "Precio por libra";
                        break;
                    case '99': // Dólar
                        $baseInfo['unit_description'] = "Valor monetario de $1.00";
                        $baseInfo['price_explanation'] = "Precio por valor monetario";
                        break;
                }
            } elseif ($product->sale_type === 'volume' && $product->volume_per_unit) {
                $baseInfo['product_measures'] = [
                    'volume_per_unit' => $product->volume_per_unit,
                    'volume_unit' => 'liters',
                    'price_per_liter' => $product->getPricePerLiter(),
                    'total_price' => $product->price
                ];
            } elseif ($product->sale_type === 'unit') {
                $baseInfo['product_measures'] = [
                    'price_per_unit' => $product->price,
                    'weight_per_unit' => $product->weight_per_unit ?? null
                ];

                switch ($unitCode) {
                    case '59': // Unidad
                        $baseInfo['unit_description'] = "1 unidad del producto";
                        $baseInfo['price_explanation'] = "Precio por unidad";
                        break;
                    case '36': // Libra
                        if ($product->weight_per_unit) {
                            $baseInfo['unit_description'] = "1 libra de producto";
                            $baseInfo['price_explanation'] = "Precio por libra";
                        } else {
                            $baseInfo['unit_description'] = "1 unidad del producto";
                            $baseInfo['price_explanation'] = "Precio por unidad";
                        }
                        break;
                    case '99': // Dólar
                        $baseInfo['unit_description'] = "Valor monetario de $1.00";
                        $baseInfo['price_explanation'] = "Precio por valor monetario";
                        break;
                }
            }
        }

        return $baseInfo;
    }

    /**
     * Verificar disponibilidad de stock en unidad específica
     */
    public function checkStockAvailability($productId, $requestedQuantity, $unitCode)
    {
        $inventory = Inventory::where('product_id', $productId)->first();

        if (!$inventory) {
            return [
                'available' => false,
                'message' => 'Producto no encontrado en inventario',
                'available_quantity' => 0
            ];
        }

        // Usar cantidad base; si es nula o <= 0, caer a cantidad legacy
        $baseQuantityAvailable = $inventory->base_quantity;
        if ($baseQuantityAvailable === null || $baseQuantityAvailable <= 0) {
            $baseQuantityAvailable = $inventory->quantity ?? 0;
        }

        // Convertir cantidad solicitada a unidad base
        $conversionData = $this->convertToBaseUnit($productId, $requestedQuantity, $unitCode);
        $requiredBaseQuantity = $conversionData['base_quantity'];

        $available = $baseQuantityAvailable >= $requiredBaseQuantity;

        // Convertir stock disponible a la unidad solicitada
        $availableInUnit = $this->convertFromBaseUnit($productId, $baseQuantityAvailable, $unitCode);

        return [
            'available' => $available,
            'message' => $available ? 'Stock disponible' : 'Stock insuficiente',
            'available_quantity' => $availableInUnit['quantity'],
            'requested_quantity' => $requestedQuantity,
            'unit_name' => $conversionData['unit_name'],
            'base_quantity_available' => $baseQuantityAvailable,
            'base_quantity_required' => $requiredBaseQuantity,
            'base_unit' => optional($inventory->baseUnit)->unit_name ?: 'Unidad'
        ];
    }

    /**
     * Obtener unidades disponibles para un producto con información de medidas
     */
    public function getAvailableUnitsForProduct($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return collect([]);
        }

        // Obtener conversiones configuradas
        $conversions = ProductUnitConversion::getActiveConversions($productId);

        // Obtener unidades por defecto según el tipo de producto
        $defaultUnits = $this->getDefaultUnitsForProductType($product);

        // Si no hay conversiones configuradas, devolver solo las unidades por defecto
        if ($conversions->isEmpty()) {
            return $defaultUnits;
        }

        // Combinar conversiones configuradas con unidades por defecto
        $allUnits = collect();

        // Agregar conversiones configuradas
        foreach ($conversions as $conversion) {
            $unitData = [
                'unit_id' => $conversion->unit_id,
                'unit_code' => $conversion->unit->unit_code,
                'unit_name' => $conversion->unit->unit_name,
                'unit_type' => $conversion->unit->unit_type,
                'conversion_factor' => $conversion->conversion_factor,
                'price_multiplier' => $conversion->price_multiplier,
                'is_default' => $conversion->is_default,
                'is_configured' => true
            ];

            // Agregar información de medidas si está disponible
            if ($product && $product->getMeasureInfo()) {
                $unitData['product_measures'] = $product->getMeasureInfo();
                $unitData['weight_info'] = [
                    'weight_lbs' => $product->weight_lbs,
                    'weight_kg' => $product->weight_kg,
                    'content_per_unit' => $product->content_per_unit
                ];
            }

            $allUnits->push($unitData);
        }

        // Agregar unidades por defecto que no estén ya incluidas
        foreach ($defaultUnits as $defaultUnit) {
            $alreadyExists = $allUnits->contains('unit_code', $defaultUnit['unit_code']);
            if (!$alreadyExists) {
                $defaultUnit['is_configured'] = false;
                $allUnits->push($defaultUnit);
            }
        }

        // Ordenar por tipo: configuradas primero, luego por defecto
        return $allUnits->sortByDesc('is_configured')->values();
    }

    /**
     * Obtener unidades por defecto según el tipo de producto
     */
    private function getDefaultUnitsForProductType($product)
    {
        $units = collect();

        // Unidades base que siempre están disponibles
        $baseUnitCodes = ['59']; // Unidad
        $dollarUnitCodes = ['99']; // Dólar

        // Agregar unidades específicas según el tipo de producto
        switch ($product->sale_type) {
            case 'volume':
                $specificUnitCodes = ['23']; // Litro
                break;
            case 'weight':
                $specificUnitCodes = ['36']; // Libra
                break;
            case 'unit':
            default:
                $specificUnitCodes = ['36']; // Libra como fallback
                break;
        }

        // Combinar todas las unidades
        $allUnitCodes = array_merge($baseUnitCodes, $specificUnitCodes, $dollarUnitCodes);

        // Obtener las unidades de la base de datos
        foreach ($allUnitCodes as $unitCode) {
            $unit = Unit::where('unit_code', $unitCode)->first();
            if ($unit) {
                $units->push([
                    'unit_id' => $unit->id,
                    'unit_code' => $unit->unit_code,
                    'unit_name' => $unit->unit_name,
                    'unit_type' => $unit->unit_type,
                    'conversion_factor' => 1.0, // Factor por defecto
                    'price_multiplier' => 1.0, // Multiplicador por defecto
                    'is_default' => $unitCode === '59' // Unidad como predeterminada
                ]);
            }
        }

        return $units;
    }

    /**
     * Calcular totales de venta con conversiones
     */
    public function calculateSaleTotals($productId, $quantity, $unitCode, $basePrice)
    {
        $conversionData = $this->convertToBaseUnit($productId, $quantity, $unitCode);
        $unitPrice = $this->calculateUnitPrice($productId, $basePrice, $unitCode);

        return [
            'quantity' => $quantity,
            'unit_name' => $conversionData['unit_name'],
            'unit_price' => $unitPrice,
            'subtotal' => $quantity * $unitPrice,
            'base_quantity_used' => $conversionData['base_quantity'],
            'conversion_factor' => $conversionData['conversion_factor']
        ];
    }

    /**
     * Calcular precio unitario por defecto cuando no hay conversiones configuradas
     */
    private function calculateDefaultUnitPrice($product, $unitCode, $basePrice)
    {
        if (!$product) {
            return $basePrice;
        }

        // Si el producto es por peso
        if ($product->sale_type === 'weight' && $product->weight_per_unit) {
            $pricePerPound = $product->getPricePerPound();

            switch ($unitCode) {
                case '36': // Libra
                    return $pricePerPound;
                case '59': // Unidad (Saco completo)
                    return $product->price;
                case '99': // Dólar
                    return 1.0;
                default:
                    return $basePrice;
            }
        }

        // Si el producto es por volumen
        if ($product->sale_type === 'volume' && $product->volume_per_unit) {
            $pricePerLiter = $product->getPricePerLiter();

            switch ($unitCode) {
                case '23': // Litro
                    return $pricePerLiter;
                case '59': // Unidad (Galón, etc.)
                    return $product->price;
                case '99': // Dólar
                    return 1.0;
                default:
                    return $basePrice;
            }
        }

        // Si el producto es por unidad
        if ($product->sale_type === 'unit') {
            switch ($unitCode) {
                case '59': // Unidad
                    return $product->price;
                case '36': // Libra
                    return $product->weight_per_unit ? $product->getPricePerPound() : $product->price;
                case '99': // Dólar
                    return 1.0;
                default:
                    return $product->price;
            }
        }

        // Fallback
        return $basePrice;
    }


}
