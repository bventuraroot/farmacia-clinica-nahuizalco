<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Sale;
use App\Models\Salesdetail;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\MedicalConsultation;
use App\Models\LabOrder;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard Central - Hub principal del sistema
     */
    public function central()
    {
        $user = auth()->user();

        // Estadísticas básicas de farmacia
        $tclientes = Client::count();
        $tproducts = Product::count();
        $totalVentasHoy = $this->getTotalVentasHoy();

        // Estadísticas de clínica
        $tpacientes = Patient::count();
        $citasHoy = Appointment::whereDate('fecha_hora', Carbon::today())->count();
        $citasPendientesHoy = Appointment::whereDate('fecha_hora', Carbon::today())
            ->whereIn('estado', ['programada', 'confirmada'])
            ->count();
        $consultasHoy = MedicalConsultation::whereDate('fecha_hora', Carbon::today())->count();

        // Estadísticas de laboratorio
        $ordenesLabHoy = LabOrder::whereDate('fecha_orden', Carbon::today())->count();
        $ordenesPendientes = LabOrder::whereIn('estado', ['pendiente', 'muestra_tomada', 'en_proceso'])->count();

        // Alertas
        $productosStockBajo = $this->getProductosStockBajo();
        $productosProximosVencer = $this->getProductosProximosVencer();

        $alertas = [
            'stockBajo' => count($productosStockBajo),
            'proximosVencer' => count($productosProximosVencer),
            'citasPendientes' => $citasPendientesHoy,
            'ordenesPendientes' => $ordenesPendientes,
        ];

        return view('dashboard-central', compact(
            'tclientes', 'tproducts', 'totalVentasHoy',
            'tpacientes', 'citasHoy', 'citasPendientesHoy', 'consultasHoy',
            'ordenesLabHoy', 'ordenesPendientes',
            'alertas'
        ));
    }

    public function home()
    {
        $user = auth()->user();

        // ========== ESTADÍSTICAS DE FARMACIA ==========
        $tclientes = Client::count();
        $tproviders = Provider::count();
        $tproducts = Product::count();
        $tsales = Sale::count();

        // Datos para gráficos de ventas
        $ventasUltimoAno = $this->getVentasUltimoAno();
        $ventasUltimoMes = $this->getVentasUltimoMes();
        $ventasUltimaSemana = $this->getVentasUltimaSemana();
        $productosMasVendidos = $this->getProductosMasVendidos();
        $ventasPorMes = $this->getVentasPorMes();
        $ventasPorDia = $this->getVentasPorDia();
        $ventasDiarias = $this->getVentasDiarias();

        // Totales de ventas
        $totalVentas = $this->getTotalVentas();
        $totalVentasMes = $this->getTotalVentasMes();
        $totalVentasSemana = $this->getTotalVentasSemana();
        $totalVentasHoy = $this->getTotalVentasHoy();

        // Crecimiento
        $crecimientoVentas = $this->calcularCrecimientoVentas();
        $crecimientoProductos = $this->calcularCrecimientoProductos();

        // Inventario y alertas
        $productosStockBajo = $this->getProductosStockBajo();
        $productosProximosVencer = $this->getProductosProximosVencer();

        // ========== ESTADÍSTICAS DE CLÍNICA ==========
        $tpacientes = Patient::count();
        $tmedicos = Doctor::where('estado', 'activo')->count();
        $citasHoy = $this->getCitasHoy();
        $citasPendientesHoy = $this->getCitasPendientesHoy();
        $consultasHoy = MedicalConsultation::whereDate('fecha_hora', Carbon::today())->count();
        $consultasMes = MedicalConsultation::whereMonth('fecha_hora', Carbon::now()->month)->count();
        
        // Próximas citas
        $proximasCitas = $this->getProximasCitas();
        
        // Estadísticas de pacientes
        $pacientesNuevosMes = Patient::whereMonth('created_at', Carbon::now()->month)->count();
        $crecimientoPacientes = $this->calcularCrecimientoPacientes();

        // ========== ESTADÍSTICAS DE LABORATORIO ==========
        $ordenesLabHoy = LabOrder::whereDate('fecha_orden', Carbon::today())->count();
        $ordenesPendientes = LabOrder::whereIn('estado', ['pendiente', 'muestra_tomada', 'en_proceso'])->count();
        $ordenesCompletadasHoy = LabOrder::whereDate('fecha_entrega_real', Carbon::today())->count();
        $ordenesMes = LabOrder::whereMonth('fecha_orden', Carbon::now()->month)->count();
        
        // Órdenes por estado
        $ordenesPorEstado = $this->getOrdenesPorEstado();
        
        // Exámenes más solicitados
        $examenesMasSolicitados = $this->getExamenesMasSolicitados();

        // ========== RESUMEN INTEGRADO ==========
        $estadisticasGenerales = [
            'farmacia' => [
                'clientes' => $tclientes,
                'proveedores' => $tproviders,
                'productos' => $tproducts,
                'ventas' => $tsales,
                'ventasHoy' => $totalVentasHoy,
                'ventasMes' => $totalVentasMes,
                'crecimiento' => $crecimientoVentas,
            ],
            'clinica' => [
                'pacientes' => $tpacientes,
                'medicos' => $tmedicos,
                'citasHoy' => $citasHoy,
                'consultasHoy' => $consultasHoy,
                'consultasMes' => $consultasMes,
                'pacientesNuevosMes' => $pacientesNuevosMes,
                'crecimiento' => $crecimientoPacientes,
            ],
            'laboratorio' => [
                'ordenesHoy' => $ordenesLabHoy,
                'pendientes' => $ordenesPendientes,
                'completadasHoy' => $ordenesCompletadasHoy,
                'ordenesMes' => $ordenesMes,
            ]
        ];

        // Alertas importantes
        $alertas = [
            'stockBajo' => $productosStockBajo->count(),
            'proximosVencer' => $productosProximosVencer->count(),
            'citasPendientes' => $citasPendientesHoy,
            'ordenesPendientes' => $ordenesPendientes,
        ];

        return view('dashboard', compact(
            // Farmacia
            'tclientes', 'tproviders', 'tproducts', 'tsales',
            'ventasUltimoAno', 'ventasUltimoMes', 'ventasUltimaSemana',
            'productosMasVendidos', 'ventasPorMes', 'ventasPorDia', 'ventasDiarias',
            'totalVentas', 'totalVentasMes', 'totalVentasSemana', 'totalVentasHoy',
            'crecimientoVentas', 'crecimientoProductos',
            'productosStockBajo', 'productosProximosVencer',
            
            // Clínica
            'tpacientes', 'tmedicos', 'citasHoy', 'citasPendientesHoy',
            'consultasHoy', 'consultasMes', 'proximasCitas',
            'pacientesNuevosMes', 'crecimientoPacientes',
            
            // Laboratorio
            'ordenesLabHoy', 'ordenesPendientes', 'ordenesCompletadasHoy',
            'ordenesMes', 'ordenesPorEstado', 'examenesMasSolicitados',
            
            // General
            'estadisticasGenerales', 'alertas'
        ));
    }

    private function getTotalVentas()
    {
        $total = DB::table('sales')
            ->join('salesdetails', 'sales.id', '=', 'salesdetails.sale_id')
            ->where('sales.state', 1)
            ->sum('salesdetails.pricesale');
        return $total ?: 0;
    }

    private function getTotalVentasMes()
    {
        $total = DB::table('sales')
            ->join('salesdetails', 'sales.id', '=', 'salesdetails.sale_id')
            ->where('sales.state', 1)
            ->whereMonth('sales.date', Carbon::now()->month)
            ->sum('salesdetails.pricesale');
        return $total ?: 0;
    }

    private function getTotalVentasSemana()
    {
        $total = DB::table('sales')
            ->join('salesdetails', 'sales.id', '=', 'salesdetails.sale_id')
            ->where('sales.state', 1)
            ->whereBetween('sales.date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('salesdetails.pricesale');
        return $total ?: 0;
    }

    private function getVentasUltimoAno()
    {
        $ventas = DB::table('sales')
            ->join('salesdetails', 'sales.id', '=', 'salesdetails.sale_id')
            ->where('sales.state', 1)
            ->whereYear('sales.date', Carbon::now()->year)
            ->selectRaw('MONTH(sales.date) as mes, SUM(salesdetails.pricesale) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $meses = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
        ];

        $resultado = [];
        foreach ($meses as $numero => $nombre) {
            $ventaMes = $ventas->where('mes', $numero)->first();
            $resultado[$nombre] = $ventaMes ? (float)$ventaMes->total : 0;
        }
        return array_values($resultado); // Para gráficos tipo array
    }

    private function getVentasUltimoMes()
    {
        $ventas = DB::table('sales')
            ->join('salesdetails', 'sales.id', '=', 'salesdetails.sale_id')
            ->where('sales.state', 1)
            ->whereMonth('sales.date', Carbon::now()->month)
            ->selectRaw('DAY(sales.date) as dia, SUM(salesdetails.pricesale) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        $diasEnMes = Carbon::now()->daysInMonth;
        $resultado = [];
        for ($i = 1; $i <= $diasEnMes; $i++) {
            $ventaDia = $ventas->where('dia', $i)->first();
            $resultado[] = $ventaDia ? (float)$ventaDia->total : 0;
        }
        return $resultado;
    }

    private function getVentasUltimaSemana()
    {
        $ventas = DB::table('sales')
            ->join('salesdetails', 'sales.id', '=', 'salesdetails.sale_id')
            ->where('sales.state', 1)
            ->whereBetween('sales.date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->selectRaw('DAY(sales.date) as dia, SUM(salesdetails.pricesale) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        $resultado = [];
        for ($i = 0; $i < 7; $i++) {
            $dia = Carbon::now()->startOfWeek()->copy()->addDays($i)->day;
            $ventaDia = $ventas->where('dia', $dia)->first();
            $resultado[] = $ventaDia ? (float)$ventaDia->total : 0;
        }
        return $resultado;
    }

    private function getProductosMasVendidos()
    {
        $productos = Salesdetail::join('products', 'salesdetails.product_id', '=', 'products.id')
            ->selectRaw('products.name, SUM(salesdetails.amountp) as cantidad_vendida')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('cantidad_vendida')
            ->limit(5)
            ->get();

        // Si no hay productos vendidos, retornar colección vacía
        return $productos->isEmpty() ? collect() : $productos;
    }

    private function getVentasPorMes()
    {
        $ventas = DB::table('sales')
            ->join('salesdetails', 'sales.id', '=', 'salesdetails.sale_id')
            ->where('sales.state', 1)
            ->whereYear('sales.date', Carbon::now()->year)
            ->selectRaw('MONTH(sales.date) as mes, SUM(salesdetails.pricesale) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $meses = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
        ];

        $resultado = [];
        foreach ($meses as $numero => $nombre) {
            $ventaMes = $ventas->where('mes', $numero)->first();
            $resultado[$nombre] = $ventaMes ? (float)$ventaMes->total : 0;
        }
        return $resultado;
    }

    private function getVentasPorDia()
    {
        $ventas = DB::table('sales')
            ->join('salesdetails', 'sales.id', '=', 'salesdetails.sale_id')
            ->where('sales.state', 1)
            ->whereMonth('sales.date', Carbon::now()->month)
            ->selectRaw('DAY(sales.date) as dia, SUM(salesdetails.pricesale) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        $diasEnMes = Carbon::now()->daysInMonth;
        $resultado = [];
        for ($i = 1; $i <= $diasEnMes; $i++) {
            $ventaDia = $ventas->where('dia', $i)->first();
            $resultado[$i] = $ventaDia ? (float)$ventaDia->total : 0;
        }
        return $resultado;
    }

    private function calcularCrecimientoVentas()
    {
        $ventasMesActual = DB::table('sales')
            ->join('salesdetails', 'sales.id', '=', 'salesdetails.sale_id')
            ->where('sales.state', 1)
            ->whereMonth('sales.date', Carbon::now()->month)
            ->sum('salesdetails.pricesale') ?: 0;

        $ventasMesAnterior = DB::table('sales')
            ->join('salesdetails', 'sales.id', '=', 'salesdetails.sale_id')
            ->where('sales.state', 1)
            ->whereMonth('sales.date', Carbon::now()->subMonth()->month)
            ->sum('salesdetails.pricesale') ?: 0;

        if ($ventasMesAnterior > 0) {
            return round((($ventasMesActual - $ventasMesAnterior) / $ventasMesAnterior) * 100, 1);
        }
        return 0;
    }

    private function calcularCrecimientoProductos()
    {
        $productosMesActual = Product::whereMonth('created_at', Carbon::now()->month)->count();
        $productosMesAnterior = Product::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();

        if ($productosMesAnterior > 0) {
            return round((($productosMesActual - $productosMesAnterior) / $productosMesAnterior) * 100, 1);
        }

        return 0;
    }

    /**
     * Obtener ventas diarias de los últimos 7 días con información detallada
     */
    private function getVentasDiarias()
    {
        $ventas = DB::table('sales')
            ->join('salesdetails', 'sales.id', '=', 'salesdetails.sale_id')
            ->where('sales.state', 1)
            ->whereBetween('sales.date', [Carbon::now()->subDays(6), Carbon::now()])
            ->selectRaw('DATE(sales.date) as fecha, SUM(salesdetails.pricesale) as total, COUNT(DISTINCT sales.id) as cantidad_ventas')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $resultado = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i)->format('Y-m-d');
            $ventaDia = $ventas->where('fecha', $fecha)->first();

            $resultado[] = [
                'fecha' => $fecha,
                'fecha_formateada' => Carbon::parse($fecha)->format('d/m'),
                'dia_semana' => Carbon::parse($fecha)->locale('es')->dayName,
                'total' => $ventaDia ? (float)$ventaDia->total : 0,
                'cantidad_ventas' => $ventaDia ? $ventaDia->cantidad_ventas : 0,
                'es_hoy' => $fecha === Carbon::now()->format('Y-m-d')
            ];
        }

        return $resultado;
    }

    /**
     * Obtener total de ventas del día actual
     */
    private function getTotalVentasHoy()
    {
        $total = DB::table('sales')
            ->join('salesdetails', 'sales.id', '=', 'salesdetails.sale_id')
            ->where('sales.state', 1)
            ->whereDate('sales.date', Carbon::now())
            ->sum('salesdetails.pricesale');
        return $total ?: 0;
    }

    // ==================== MÉTODOS DE CLÍNICA ====================

    /**
     * Obtener citas del día actual
     */
    private function getCitasHoy()
    {
        return Appointment::whereDate('fecha_hora', Carbon::today())->count();
    }

    /**
     * Obtener citas pendientes del día
     */
    private function getCitasPendientesHoy()
    {
        return Appointment::whereDate('fecha_hora', Carbon::today())
            ->whereIn('estado', ['programada', 'confirmada'])
            ->count();
    }

    /**
     * Obtener próximas citas (próximas 24 horas)
     */
    private function getProximasCitas()
    {
        return Appointment::with(['patient', 'doctor'])
            ->where('fecha_hora', '>=', Carbon::now())
            ->where('fecha_hora', '<=', Carbon::now()->addHours(24))
            ->whereIn('estado', ['programada', 'confirmada'])
            ->orderBy('fecha_hora')
            ->limit(5)
            ->get();
    }

    /**
     * Calcular crecimiento de pacientes
     */
    private function calcularCrecimientoPacientes()
    {
        $pacientesMesActual = Patient::whereMonth('created_at', Carbon::now()->month)->count();
        $pacientesMesAnterior = Patient::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();

        if ($pacientesMesAnterior > 0) {
            return round((($pacientesMesActual - $pacientesMesAnterior) / $pacientesMesAnterior) * 100, 1);
        }
        return 0;
    }

    // ==================== MÉTODOS DE LABORATORIO ====================

    /**
     * Obtener órdenes de laboratorio por estado
     */
    private function getOrdenesPorEstado()
    {
        return LabOrder::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();
    }

    /**
     * Obtener exámenes más solicitados
     */
    private function getExamenesMasSolicitados()
    {
        return DB::table('lab_order_exams')
            ->join('lab_exams', 'lab_order_exams.exam_id', '=', 'lab_exams.id')
            ->select('lab_exams.nombre', DB::raw('count(*) as cantidad'))
            ->groupBy('lab_exams.id', 'lab_exams.nombre')
            ->orderByDesc('cantidad')
            ->limit(5)
            ->get();
    }

    // ==================== MÉTODOS DE INVENTARIO/FARMACIA ====================

    /**
     * Obtener productos con stock bajo
     */
    private function getProductosStockBajo()
    {
        return Inventory::with('product')
            ->whereColumn('quantity', '<=', 'minimum_stock')
            ->where('quantity', '>', 0)
            ->orderBy('quantity')
            ->limit(10)
            ->get();
    }

    /**
     * Obtener productos próximos a vencer (próximos 30 días)
     */
    private function getProductosProximosVencer()
    {
        $fechaLimite = Carbon::now()->addDays(30);
        
        return Inventory::with('product')
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<=', $fechaLimite)
            ->where('expiration_date', '>=', Carbon::now())
            ->orderBy('expiration_date')
            ->limit(10)
            ->get();
    }
}
