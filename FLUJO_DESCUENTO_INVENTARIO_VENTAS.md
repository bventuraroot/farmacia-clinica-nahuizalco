# Flujo de Descuento de Inventario en Ventas y DTE

## Resumen del Flujo

**El inventario se descuenta CUANDO LA VENTA SE FINALIZA**, no cuando se emite el DTE. El DTE es solo la representación electrónica del documento fiscal, pero el descuento del inventario ocurre en el momento de la finalización de la venta.

## Flujo Detallado

### 1. Creación de Venta (Sin Descuento)
- Usuario crea una venta en el sistema
- Se registran los productos y cantidades
- **NO se descuenta inventario** en este momento
- La venta queda como "borrador" o "pendiente"

### 2. Finalización de Venta (AQUÍ SE DESCUENTA)
- Usuario finaliza la venta (proceso de "createdocument")
- **En este momento se descuenta el inventario**
- La venta cambia a `typesale = 1` (finalizada)
- Se asigna número de documento correlativo

### 3. Emisión de DTE (Solo Documento Fiscal)
- Si está habilitado DTE para la empresa, se emite el documento electrónico
- **El inventario YA fue descontado en el paso anterior**
- El DTE es solo la representación fiscal del documento ya finalizado

## Código Clave - SaleController.php

### Función `createdocument()` - Líneas 1188-1222
```php
public function createdocument($corr, $amount)
{
    DB::beginTransaction();
    try {
        $salesave = Sale::find(base64_decode($corr));
        $salesave->totalamount = $amount;
        $salesave->typesale = 1; // Cambiar a venta finalizada
        
        // ... lógica de correlativo ...
        
        $salesave->nu_doc = $newCorr[0]->actual;
        $salesave->save();

        // ⭐ AQUÍ SE DESCUENTA EL INVENTARIO ⭐
        $this->deductInventoryFromFinalizedSale($salesave->id);
        
        // ... resto del proceso incluyendo DTE ...
        
        DB::commit();
    } catch (\Exception $e) {
        DB::rollback();
        throw $e;
    }
}
```

### Función `deductInventoryFromFinalizedSale()` - Líneas 4585-4625
```php
protected function deductInventoryFromFinalizedSale($saleId)
{
    try {
        Log::info("🔄 Descontando inventario para venta finalizada ID: {$saleId}");

        // Obtener todos los detalles de la venta
        $saleDetails = Salesdetail::where('sale_id', $saleId)->get();

        foreach ($saleDetails as $detail) {
            $product = $detail->product;
            $quantity = $detail->amountp;
            $unitId = $detail->unit_id;
            $conversionFactor = $detail->conversion_factor;

            // Obtener el código de unidad desde el unit_id
            $unit = \App\Models\Unit::find($unitId);
            $unitCode = $unit ? $unit->unit_code : null;

            // Descontar del inventario usando la función existente
            $this->deductFromInventory($product->id, $quantity, $unitCode, $unitId, $conversionFactor);
        }

        Log::info("✅ Inventario descontado exitosamente para venta ID: {$saleId}");

    } catch (\Exception $e) {
        Log::error("❌ Error descontando inventario para venta {$saleId}: " . $e->getMessage());
        throw $e;
    }
}
```

### Función `deductFromInventory()` - Líneas 4394-4460
```php
private function deductFromInventory($productId, $quantity, $unitCode, $unitId, $conversionFactor)
{
    try {
        // Obtener el producto y su inventario
        $product = \App\Models\Product::find($productId);
        $inventory = $product->inventory;

        // Crear inventario automáticamente si no existe
        if (!$inventory) {
            $inventory = new \App\Models\Inventory();
            $inventory->product_id = $productId;
            $inventory->quantity = 0;
            $inventory->base_quantity = 0;
            $inventory->save();
        }

        // Calcular cantidad base a descontar usando UnitConversionService
        $baseQuantityToDeduct = $this->unitConversionService->calculateBaseQuantityNeeded($productId, $quantity, $unitCode);

        // Descontar del inventario (en base)
        if (isset($inventory->base_quantity)) {
            $inventory->base_quantity = (float)$inventory->base_quantity - $baseQuantityToDeduct;
        }
        // Mantener compatibilidad con quantity legado
        if (isset($inventory->quantity)) {
            $inventory->quantity = (float)$inventory->quantity - $baseQuantityToDeduct;
        }
        
        $inventory->save();

    } catch (\Exception $e) {
        \Log::error("Error al descontar inventario: " . $e->getMessage());
        throw $e;
    }
}
```

## Conversión de Unidades

### UnitConversionService - `calculateBaseQuantityNeeded()`
El sistema maneja diferentes tipos de productos y sus conversiones:

#### Productos por Peso (weight)
- **Inventario en Sacos**: 1 saco = 50 libras (ejemplo)
- **Venta en Libras**: Se convierte a sacos base
- **Venta en Sacos**: 1 saco = 1 saco base

#### Productos por Volumen (volume)
- **Inventario en Depósitos**: 1 depósito = 200 litros (ejemplo)
- **Venta en Litros**: Se convierte a depósitos base
- **Venta en Depósitos**: 1 depósito = 1 depósito base

#### Productos por Unidad (unit)
- **Inventario en Unidades**: 1 unidad = 1 unidad base
- **Venta en Unidades**: 1 unidad = 1 unidad base

## Momentos del Flujo

### ❌ NO se descuenta inventario cuando:
1. Se crea la venta inicial
2. Se agregan productos a la venta
3. Se modifica la venta
4. Se guarda como borrador

### ✅ SÍ se descuenta inventario cuando:
1. **Se finaliza la venta** (función `createdocument`)
2. Se asigna número de documento
3. Se cambia `typesale` a 1 (finalizada)

### 📄 DTE es independiente:
- Se emite DESPUÉS del descuento de inventario
- Es solo la representación fiscal del documento
- No afecta el inventario (ya fue descontado)

## Ejemplo Práctico

```
1. Usuario crea venta: 2 sacos de fertilizante
   → Inventario: 100 sacos (SIN CAMBIOS)

2. Usuario finaliza la venta
   → Se ejecuta deductInventoryFromFinalizedSale()
   → Se descuenta: 2 sacos del inventario
   → Inventario: 98 sacos
   → Venta marcada como finalizada (typesale = 1)

3. Sistema emite DTE (si está habilitado)
   → Se genera documento electrónico
   → Inventario: 98 sacos (SIN CAMBIOS ADICIONALES)
```

## Logs del Sistema

El sistema registra todos los movimientos de inventario:

```
🔄 Descontando inventario para venta finalizada ID: 123
📦 Procesando detalle: Producto FERTILIZANTE, Cantidad: 2, Unidad ID: 28, Código: 59
📦 Validación de stock: base_quantity_needed: 2, current_stock: 100, is_sufficient: true
✅ Inventario descontado exitosamente para venta ID: 123
```

## Consideraciones Importantes

### 1. Transacciones de Base de Datos
- Todo el proceso está dentro de una transacción (`DB::beginTransaction()`)
- Si falla el descuento de inventario, se revierte toda la operación
- Garantiza consistencia de datos

### 2. Inventarios Negativos
- El sistema permite inventarios negativos
- Solo registra warnings en los logs
- No bloquea la venta por falta de stock

### 3. Conversión Automática
- Se crea inventario automáticamente si no existe
- Se manejan conversiones entre diferentes unidades
- Se mantiene compatibilidad con sistema legacy

### 4. Logging Completo
- Se registran todos los movimientos
- Facilita auditoría y debugging
- Permite rastrear problemas de inventario

## Conclusión

**El inventario se descuenta en el momento de la finalización de la venta, NO cuando se emite el DTE.** El DTE es únicamente la representación electrónica del documento fiscal, pero el descuento del inventario ocurre antes, cuando la venta se marca como finalizada (`typesale = 1`).

Este flujo garantiza que:
- El inventario se actualice inmediatamente al finalizar la venta
- El DTE solo represente fiscalmente una venta ya procesada
- Se mantenga la consistencia entre ventas e inventario
- Se permita la auditoría completa del proceso
