<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
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

        // Órdenes de clínica por facturar (consultas completadas sin factura)
        $consultasPorFacturar = MedicalConsultation::with(['patient', 'doctor'])
            ->where('estado', 'finalizada')
            ->whereDoesntHave('patient', function($query) {
                // Aquí puedes agregar lógica para verificar si ya fue facturada
            })
            ->orderBy('fecha_hora', 'desc')
            ->limit(50)
            ->get();

        // Órdenes de laboratorio por facturar (completadas sin factura)
        $ordenesLabPorFacturar = LabOrder::with(['patient', 'doctor', 'exams.exam'])
            ->where('estado', 'completada')
            ->whereDoesntHave('patient', function($query) {
                // Lógica para verificar si ya fue facturada
            })
            ->orderBy('fecha_entrega_real', 'desc')
            ->limit(50)
            ->get();

        // Productos de farmacia (para venta directa)
        $productos = Product::where('amountp', '>', 0)
            ->orderBy('name')
            ->limit(100)
            ->get();

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
     */
    public function getConsultasPendientes()
    {
        $consultas = MedicalConsultation::with(['patient', 'doctor'])
            ->where('estado', 'finalizada')
            ->orderBy('fecha_hora', 'desc')
            ->get();

        return response()->json($consultas);
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
     */
    public function facturarConsulta(Request $request, $consultaId)
    {
        $consulta = MedicalConsultation::with(['patient', 'doctor'])->findOrFail($consultaId);

        // Crear venta para la consulta
        $sale = Sale::create([
            'client_id' => $consulta->patient->id, // Asumiendo que paciente puede ser cliente
            'user_id' => auth()->id(),
            'company_id' => $consulta->company_id,
            'date' => now(),
            'num_document' => $this->generateInvoiceNumber(),
            'type_document' => 'Factura Consulta',
            'state' => 1,
            // Agregar otros campos necesarios según tu estructura
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Consulta facturada exitosamente',
            'sale_id' => $sale->id
        ]);
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
            // Obtener precio de consulta según especialidad o configuración
            $precioBase = 25.00; // Precio base de consulta
            return response()->json(['precio' => $precioBase]);
        }

        if ($tipo === 'laboratorio') {
            $orden = LabOrder::with('exams.exam')->find($id);
            return response()->json(['precio' => $orden->total]);
        }

        return response()->json(['precio' => 0]);
    }
}

