<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseDetail;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Traits\BasicImageHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    use BasicImageHandler;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         //dd(Client::where('company_id', base64_decode($company))->get());
         $products = Product::join('providers', 'products.provider_id', '=', 'providers.id')
         ->join('marcas', 'products.marca_id', '=', 'marcas.id')
         ->select('providers.razonsocial as nameprovider', 'providers.id as idprovider', 'products.*', 'marcas.name as marcaname')
         ->get();
            return view('products.index', array(
                "products" => $products
            ));
    }

    public function getproductid($id){
        $provider = Product::join('providers', 'products.provider_id', '=', 'providers.id')
        ->join('marcas', 'products.marca_id', '=', 'marcas.id')
        ->select('products.id as productid',  DB::raw('products.name as productname'), 'products.*', 'marcas.name as marcaname')
        ->where('products.id', '=', base64_decode($id))
        ->get();
        return response()->json($provider);
    }

    public function getproductcode($code){
        try {
            // Decodificar el código que viene codificado en base64 y URL encoded
            $decodedCode = base64_decode($code);
            $decodedCode = urldecode($decodedCode);

            $product = Product::join('providers', 'products.provider_id', '=', 'providers.id')
            ->join('marcas', 'products.marca_id', '=', 'marcas.id')
            ->select('products.id as productid',  DB::raw('products.name as productname'), 'products.*', 'marcas.name as marcaname', 'providers.razonsocial as provider')
            ->where('products.code', '=', $decodedCode)
            ->get();

            return response()->json($product);
        } catch (\Exception $e) {
            Log::error('Error en getproductcode: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    public function getproductall(){
        $product = Product::join('providers', 'products.provider_id', '=', 'providers.id')
        ->select('providers.razonsocial as nameprovider', 'providers.id as idprovider', 'products.*')
         ->get();
        return response()->json($product);
    }

    public function getproductbyid($id){
        $product = Product::find($id);
        return response()->json($product);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        try {
            $product = new Product();
            $product->code = $request->code;
            $product->name = $request->name;
            $product->state = 1;
            $product->cfiscal = $request->cfiscal;
            $product->type = $request->type;
            $product->price = $request->price;
            $product->marca_id = $request->marca ?: 1; // Usar marca por defecto si no se selecciona
            $product->provider_id = $request->provider ?: 1; // Usar proveedor por defecto si no se selecciona
            $product->category = $request->category;
            $product->description = $request->description;
            $product->user_id = auth()->id();

            // ===== Campos de unidades de medida (creación) =====
            // Estos campos son opcionales y se usan cuando sale_type es 'weight' o 'volume'
            if ($request->has('sale_type')) {
                $product->sale_type = $request->input('sale_type');
            }
            if ($request->has('weight_per_unit')) {
                $product->weight_per_unit = $request->input('weight_per_unit');
            }
            if ($request->has('volume_per_unit')) {
                $product->volume_per_unit = $request->input('volume_per_unit');
            }
            if ($request->has('content_per_unit')) {
                $product->content_per_unit = $request->input('content_per_unit');
            }

            // Procesar imagen
            $nombre = "none.jpg";
            if($request->hasFile("image")){
                $imageData = $this->storeImage(
                    $request->file("image"),
                    'products',
                    [
                        'path' => ''
                    ]
                );
                $nombre = $imageData['filename'];
            }

            $product->image = $nombre;
            $product->save();

            // Crear conversiones de unidades automáticamente
            Log::info("Intentando crear conversiones automáticas para producto ID: {$product->id}");
            $this->createDefaultUnitConversions($product);
            Log::info("Conversiones automáticas creadas para producto ID: {$product->id}");

            return redirect()->route('product.index')->with('success', 'Producto creado exitosamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al crear el producto: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductUpdateRequest $request)
    {
        try {
            $product = Product::findOrFail($request->idedit);
            $product->code = $request->codeedit;
            $product->name = $request->nameedit;
            $product->cfiscal = $request->cfiscaledit;
            $product->type = $request->typeedit;
            $product->category = $request->categoryedit;
            // Guardar precio total (requerido para conversiones)
            if ($request->filled('priceedit')) {
                $product->price = $request->priceedit;
            }
            $product->marca_id = $request->marcaredit ?: 1;
            $product->provider_id = $request->provideredit ?: 1;
            $product->description = $request->descriptionedit;
            $product->user_id = auth()->id();

            // ===== Campos de unidades de medida (edición) =====
            if ($request->has('sale_type_edit')) {
                $product->sale_type = $request->input('sale_type_edit');
            }
            if ($request->has('weight_per_unit_edit')) {
                $product->weight_per_unit = $request->input('weight_per_unit_edit');
            }
            if ($request->has('volume_per_unit_edit')) {
                $product->volume_per_unit = $request->input('volume_per_unit_edit');
            }
            if ($request->has('content_per_unit_edit')) {
                $product->content_per_unit = $request->input('content_per_unit_edit');
            }

            // Manejar imagen
            $nombre = "none.jpg";
            $imagenOriginal = $request->imageeditoriginal;

            if($request->hasFile("imageedit")){
                $imagen = $request->file("imageedit");
                $imageData = $this->storeImage(
                    $imagen,
                    'products',
                    [
                        'path' => ''
                    ]
                );
                $nombre = $imageData['filename'];
            } else {
                // No se subió nueva imagen, mantener la imagen actual
                if($imagenOriginal && $imagenOriginal !== 'null' && $imagenOriginal !== '') {
                    $nombre = $imagenOriginal;
                }
            }

            $product->image = $nombre;
            $product->save();

            // Crear o actualizar conversiones de unidades automáticamente
            Log::info("Intentando crear/actualizar conversiones automáticas para producto ID: {$product->id}");
            $this->createDefaultUnitConversions($product);
            Log::info("Conversiones automáticas creadas/actualizadas para producto ID: {$product->id}");

            return redirect()->route('product.index')->with('success', 'Producto actualizado exitosamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar el producto: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         //dd($id);
         $Product = Product::find(base64_decode($id));
         $Product->delete();
         return response()->json(array(
             "res" => "1"
         ));
    }

    public function toggleState(Request $request, $id)
    {
        try {
            $product = Product::findOrFail(base64_decode($id));
            $product->state = $request->state;
            $product->save();

            return response()->json(array(
                "res" => "1"
            ));
        } catch (\Exception $e) {
            return response()->json(array(
                "res" => "0"
            ));
        }
    }

    /**
     * Mostrar seguimiento de vencimiento de un producto específico
     */
    public function expirationTracking($productId)
    {
        try {
            $product = Product::with(['provider'])->findOrFail($productId);

            // Obtener todos los detalles de compra para este producto
            $purchaseDetails = PurchaseDetail::where('product_id', $productId)
                ->where('quantity', '>', 0)
                ->with(['purchase', 'purchase.provider'])
                ->orderBy('expiration_date', 'asc')
                ->get();

            // Agrupar por estado de vencimiento
            $expired = $purchaseDetails->filter(function($detail) {
                return $detail->isExpired();
            });

            $critical = $purchaseDetails->filter(function($detail) {
                return $detail->isExpiringSoon(7) && !$detail->isExpired();
            });

            $warning = $purchaseDetails->filter(function($detail) {
                return $detail->isExpiringSoon(30) && !$detail->isExpiringSoon(7) && !$detail->isExpired();
            });

            $ok = $purchaseDetails->filter(function($detail) {
                return !$detail->isExpiringSoon(30) && !$detail->isExpired();
            });

            $html = view('products.partials.expiration-tracking', compact(
                'product',
                'purchaseDetails',
                'expired',
                'critical',
                'warning',
                'ok'
            ))->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar el seguimiento de vencimiento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si un código de producto ya existe
     */
    public function checkCodeExists(Request $request)
    {
        try {
            $code = trim($request->input('code'));
            $excludeId = $request->input('exclude_id'); // Para excluir el producto actual en edición

            if (empty($code)) {
                return response()->json([
                    'exists' => false,
                    'message' => 'Código vacío'
                ]);
            }

            $query = Product::where('code', $code);

            // Si se está editando un producto, excluir su ID
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            $exists = $query->exists();

            return response()->json([
                'exists' => $exists,
                'message' => $exists ? 'El código ya existe' : 'Código disponible'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'exists' => false,
                'message' => 'Error al verificar el código: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear conversiones de unidades por defecto para un producto
     */
    private function createDefaultUnitConversions($product)
    {
        try {
            // Obtener las unidades básicas
            $units = \App\Models\Unit::whereIn('unit_code', ['59', '36', '34', '99'])->get();

            if ($units->isEmpty()) {
                Log::warning("No se encontraron unidades básicas para crear conversiones para el producto {$product->id}");
                return;
            }

            // Crear conversiones según el tipo de venta
            if ($product->sale_type === 'weight' && $product->weight_per_unit) {
                $this->createWeightConversions($product, $units);
            } elseif ($product->sale_type === 'volume' && $product->volume_per_unit) {
                $this->createVolumeConversions($product, $units);
            } else {
                $this->createUnitConversions($product, $units);
            }

            Log::info("Conversiones de unidades creadas automáticamente para el producto {$product->id} ({$product->name})");

        } catch (\Exception $e) {
            Log::error("Error al crear conversiones de unidades para el producto {$product->id}: " . $e->getMessage());
        }
    }

    /**
     * Crear conversiones para productos por peso
     */
    private function createWeightConversions($product, $units)
    {
        $weightPerUnit = $product->weight_per_unit; // Libras por unidad base
        $pricePerUnit = $product->price; // Precio por unidad base

        foreach ($units as $unit) {
            $conversionFactor = 1.0;
            $priceMultiplier = 1.0;

            switch ($unit->unit_code) {
                case '59': // Unidad (Saco completo)
                    $conversionFactor = 1.0;
                    $priceMultiplier = 1.0;
                    $isDefault = true;
                    break;

                case '36': // Libra
                    $conversionFactor = 1.0 / $weightPerUnit; // 1 libra = 1/weightPerUnit sacos
                    $priceMultiplier = 1.0 / $weightPerUnit; // Precio por libra
                    $isDefault = false;
                    break;

                case '34': // Kilogramo
                    $conversionFactor = 2.2046 / $weightPerUnit; // 1 kg = 2.2046 libras
                    $priceMultiplier = 2.2046 / $weightPerUnit; // Precio por kg
                    $isDefault = false;
                    break;

                case '99': // Dólar (valor monetario)
                    $conversionFactor = 1.0 / $pricePerUnit; // 1 dólar = 1/precio sacos
                    $priceMultiplier = 1.0 / $pricePerUnit; // Valor por dólar
                    $isDefault = false;
                    break;

                default:
                    $conversionFactor = 1.0;
                    $priceMultiplier = 1.0;
                    $isDefault = false;
                    break;
            }

            \App\Models\ProductUnitConversion::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'unit_id' => $unit->id,
                ],
                [
                    'conversion_factor' => $conversionFactor,
                    'price_multiplier' => $priceMultiplier,
                    'is_default' => $isDefault,
                    'is_active' => true,
                    'notes' => 'Creado automáticamente al crear el producto'
                ]
            );
        }
    }

    /**
     * Crear conversiones para productos por volumen
     */
    private function createVolumeConversions($product, $units)
    {
        $volumePerUnit = $product->volume_per_unit; // Litros por unidad base
        $pricePerUnit = $product->price; // Precio por unidad base

        foreach ($units as $unit) {
            $conversionFactor = 1.0;
            $priceMultiplier = 1.0;

            switch ($unit->unit_code) {
                case '59': // Unidad (Galón, etc.)
                    $conversionFactor = 1.0;
                    $priceMultiplier = 1.0;
                    $isDefault = true;
                    break;

                case '23': // Litro
                    $conversionFactor = 1.0 / $volumePerUnit; // 1 litro = 1/volumePerUnit unidades
                    $priceMultiplier = 1.0 / $volumePerUnit; // Precio por litro
                    $isDefault = false;
                    break;

                case '26': // Mililitro
                    $conversionFactor = 0.001 / $volumePerUnit; // 1 ml = 0.001 litros
                    $priceMultiplier = 0.001 / $volumePerUnit; // Precio por ml
                    $isDefault = false;
                    break;

                case '99': // Dólar
                    $conversionFactor = 1.0 / $pricePerUnit;
                    $priceMultiplier = 1.0 / $pricePerUnit;
                    $isDefault = false;
                    break;

                default:
                    $conversionFactor = 1.0;
                    $priceMultiplier = 1.0;
                    $isDefault = false;
                    break;
            }

            \App\Models\ProductUnitConversion::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'unit_id' => $unit->id,
                ],
                [
                    'conversion_factor' => $conversionFactor,
                    'price_multiplier' => $priceMultiplier,
                    'is_default' => $isDefault,
                    'is_active' => true,
                    'notes' => 'Creado automáticamente al crear el producto'
                ]
            );
        }
    }

    /**
     * Crear conversiones para productos por unidad
     */
    private function createUnitConversions($product, $units)
    {
        foreach ($units as $unit) {
            $conversionFactor = 1.0;
            $priceMultiplier = 1.0;

            switch ($unit->unit_code) {
                case '59': // Unidad
                    $conversionFactor = 1.0;
                    $priceMultiplier = 1.0;
                    $isDefault = true;
                    break;

                case '99': // Dólar
                    $conversionFactor = 1.0 / $product->price;
                    $priceMultiplier = 1.0 / $product->price;
                    $isDefault = false;
                    break;

                default:
                    $conversionFactor = 1.0;
                    $priceMultiplier = 1.0;
                    $isDefault = false;
                    break;
            }

            \App\Models\ProductUnitConversion::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'unit_id' => $unit->id,
                ],
                [
                    'conversion_factor' => $conversionFactor,
                    'price_multiplier' => $priceMultiplier,
                    'is_default' => $isDefault,
                    'is_active' => true,
                    'notes' => 'Creado automáticamente al crear el producto'
                ]
            );
        }
    }
}
