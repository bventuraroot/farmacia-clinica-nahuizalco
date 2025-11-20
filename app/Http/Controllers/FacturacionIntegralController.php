<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Appointment;
use App\Models\MedicalConsultation;
use App\Models\LabOrder;
use App\Models\LabOrderExam;
use App\Models\Client;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FacturacionIntegralController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sale.index')->only(['index']);
        $this->middleware('permission:sale.create')->only(['create', 'store']);
    }

    /**
     * Vista principal de facturación integral
     */
    public function index(Request $request)
    {
        $tipo = $request->get('tipo', 'farmacia');

        // Estadísticas generales
        $ventasHoy = Sale::whereDate('date', Carbon::today())->count();
        $totalHoy = DB::table('sales')
            ->join('salesdetails', 'sales.id', '=', 'salesdetails.sale_id')
            ->where('sales.state', 1)
            ->whereDate('sales.date', Carbon::today())
            ->sum('salesdetails.pricesale') ?: 0;

        // Las consultas médicas NO se facturan - solo control clínico
        $consultasPorFacturar = collect([]);

        // Órdenes de laboratorio por facturar (completadas sin factura)
        $ordenesLabPorFacturar = LabOrder::with(['patient', 'doctor', 'exams.exam'])
            ->where('estado', 'completada')
            ->whereDoesntHave('patient', function($query) {
                // Lógica para verificar si ya fue facturada
            })
            ->orderBy('fecha_entrega_real', 'desc')
            ->limit(50)
            ->get();

        // Productos de farmacia (para venta directa) - Usar Inventory en lugar de Product
        $productos = Inventory::with('product')
            ->where('quantity', '>', 0)
            ->orderBy('quantity', 'desc')
            ->limit(100)
            ->get()
            ->map(function($inventory) {
                return [
                    'id' => $inventory->product_id,
                    'name' => $inventory->product->name ?? 'Sin nombre',
                    'quantity' => $inventory->quantity,
                    'price' => $inventory->product->pricesale ?? 0,
                    'product' => $inventory->product
                ];
            });

        return view('facturacion.integral', compact(
            'tipo',
            'ventasHoy',
            'totalHoy',
            'consultasPorFacturar',
            'ordenesLabPorFacturar',
            'productos'
        ));
    }

    /**
     * Obtener consultas médicas pendientes de facturar
     * NOTA: Las consultas médicas NO se facturan - solo control clínico
     */
    public function getConsultasPendientes()
    {
        // Las consultas médicas no se facturan
        return response()->json([]);
    }

    /**
     * Obtener órdenes de laboratorio pendientes de facturar
     */
    public function getOrdenesLabPendientes()
    {
        $ordenes = LabOrder::with(['patient', 'doctor', 'exams.exam'])
            ->where('estado', 'completada')
            ->orderBy('fecha_entrega_real', 'desc')
            ->get();

        return response()->json($ordenes);
    }

    /**
     * Crear factura desde consulta médica
     * NOTA: Las consultas médicas NO se facturan - solo control clínico
     */
    public function facturarConsulta(Request $request, $consultaId)
    {
        return response()->json([
            'success' => false,
            'message' => 'Las consultas médicas no se facturan. El módulo de clínica es solo para control de citas, pacientes y consultas médicas.'
        ], 403);
    }

    /**
     * Crear factura desde orden de laboratorio
     */
    public function facturarOrdenLab(Request $request, $ordenId)
    {
        $orden = LabOrder::with(['patient', 'exams.exam'])->findOrFail($ordenId);

        // Crear venta para la orden de laboratorio
        $sale = Sale::create([
            'client_id' => $orden->patient->id,
            'user_id' => auth()->id(),
            'company_id' => $orden->company_id,
            'date' => now(),
            'num_document' => $this->generateInvoiceNumber(),
            'type_document' => 'Factura Laboratorio',
            'state' => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Orden de laboratorio facturada exitosamente',
            'sale_id' => $sale->id
        ]);
    }

    /**
     * Generar número de factura único
     */
    private function generateInvoiceNumber()
    {
        $lastSale = Sale::orderBy('id', 'desc')->first();
        $nextNumber = $lastSale ? ($lastSale->id + 1) : 1;
        return 'FAC-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Obtener precio de servicio (consulta o examen)
     */
    public function getPrecioServicio(Request $request)
    {
        $tipo = $request->get('tipo'); // 'consulta' o 'laboratorio'
        $id = $request->get('id');

        if ($tipo === 'consulta') {
            // Las consultas médicas no se facturan
            return response()->json(['precio' => 0]);
        }

        if ($tipo === 'laboratorio') {
            $orden = LabOrder::with('exams.exam')->find($id);
            return response()->json(['precio' => $orden->total]);
        }

        return response()->json(['precio' => 0]);
    }
}

