@extends('layouts/layoutMaster')

@section('title', 'Dashboard')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
<script>
    window.ventasUltimoAno = @json($ventasUltimoAno);
    window.ventasUltimoMes = @json($ventasUltimoMes);
    window.ventasUltimaSemana = @json($ventasUltimaSemana);
    window.ventasPorMes = @json($ventasPorMes);
    window.ventasPorDia = @json($ventasPorDia);
    window.ventasDiarias = @json($ventasDiarias);
    window.productosMasVendidos = @json($productosMasVendidos);
</script>
<script src="{{asset('assets/js/dashboards-crm.js')}}"></script>
@endsection

@section('content')
<div class="row">
  <!-- Estadísticas Generales -->
  <div class="mb-4 col-12">
    <div class="row">
      <div class="mb-4 col-xl-3 col-md-6">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div class="gap-3 d-flex align-items-center">
                <div class="avatar">
                  <span class="rounded avatar-initial bg-label-primary">
                    <i class="ti ti-users ti-sm"></i>
                  </span>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">{{ $tclientes }}</h5>
                  <small>Clientes</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="mb-4 col-xl-3 col-md-6">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div class="gap-3 d-flex align-items-center">
                <div class="avatar">
                  <span class="rounded avatar-initial bg-label-success">
                    <i class="ti ti-truck ti-sm"></i>
                  </span>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">{{ $tproviders }}</h5>
                  <small>Proveedores</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="mb-4 col-xl-3 col-md-6">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div class="gap-3 d-flex align-items-center">
                <div class="avatar">
                  <span class="rounded avatar-initial bg-label-warning">
                    <i class="ti ti-package ti-sm"></i>
                  </span>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">{{ $tproducts }}</h5>
                  <small>Productos</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="mb-4 col-xl-3 col-md-6">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div class="gap-3 d-flex align-items-center">
                <div class="avatar">
                  <span class="rounded avatar-initial bg-label-info">
                    <i class="ti ti-shopping-cart ti-sm"></i>
                  </span>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">{{ $tsales }}</h5>
                  <small>Ventas</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Estadísticas de Ventas Diarias -->
  <div class="mb-4 col-12">
    <div class="row">
      <div class="mb-4 col-xl-3 col-md-6">
        <div class="card border-primary">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div class="gap-3 d-flex align-items-center">
                <div class="avatar">
                  <span class="rounded avatar-initial bg-label-primary">
                    <i class="ti ti-calendar-event ti-sm"></i>
                  </span>
                </div>
                <div class="card-info">
                  <h5 class="mb-0 text-primary">${{ number_format($totalVentasHoy, 2) }}</h5>
                  <small>Ventas Hoy</small>
                </div>
              </div>
              <div class="text-end">
                <span class="badge bg-label-primary">Hoy</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="mb-4 col-xl-3 col-md-6">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div class="gap-3 d-flex align-items-center">
                <div class="avatar">
                  <span class="rounded avatar-initial bg-label-success">
                    <i class="ti ti-calendar-week ti-sm"></i>
                  </span>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">${{ number_format($totalVentasSemana, 2) }}</h5>
                  <small>Esta Semana</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="mb-4 col-xl-3 col-md-6">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div class="gap-3 d-flex align-items-center">
                <div class="avatar">
                  <span class="rounded avatar-initial bg-label-warning">
                    <i class="ti ti-calendar-month ti-sm"></i>
                  </span>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">${{ number_format($totalVentasMes, 2) }}</h5>
                  <small>Este Mes</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="mb-4 col-xl-3 col-md-6">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div class="gap-3 d-flex align-items-center">
                <div class="avatar">
                  <span class="rounded avatar-initial bg-label-info">
                    <i class="ti ti-chart-line ti-sm"></i>
                  </span>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">${{ number_format($totalVentas, 2) }}</h5>
                  <small>Total General</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Ventas del último año -->
  <div class="mb-4 col-xl-2 col-md-4 col-6">
    <div class="card">
      <div class="pb-0 card-header">
        <h5 class="mb-0 card-title">Ventas</h5>
        <small class="text-muted">Último Año</small>
      </div>
      <div id="salesLastYear"></div>
      <div class="pt-0 card-body">
        <div class="gap-3 mt-3 d-flex justify-content-between align-items-center">
          <h4 class="mb-0">${{ number_format($totalVentas, 2) }}</h4>
          <small class="{{ $crecimientoVentas >= 0 ? 'text-success' : 'text-danger' }}">
            {{ $crecimientoVentas >= 0 ? '+' : '' }}{{ $crecimientoVentas }}%
          </small>
        </div>
      </div>
    </div>
  </div>

  <!-- Ventas del último mes -->
  <div class="mb-4 col-xl-2 col-md-4 col-6">
    <div class="card">
      <div class="pb-0 card-header">
        <h5 class="mb-0 card-title">Ventas</h5>
        <small class="text-muted">Último Mes</small>
      </div>
      <div class="card-body">
        <div id="sessionsLastMonth"></div>
        <div class="gap-3 mt-3 d-flex justify-content-between align-items-center">
          <h4 class="mb-0">${{ number_format($totalVentasMes, 2) }}</h4>
          <small class="text-success">+{{ $crecimientoVentas }}%</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Beneficio Total -->
  <div class="mb-4 col-xl-2 col-md-4 col-6">
    <div class="card">
      <div class="card-body">
        <div class="p-2 mb-2 rounded badge bg-label-danger"><i class="ti ti-currency-dollar ti-md"></i></div>
        <h5 class="pt-2 mb-1 card-title">Beneficio Total</h5>
        <small class="text-muted">Última semana</small>
        <p class="mt-1 mb-2">${{ number_format($totalVentasSemana * 0.3, 2) }}</p>
        <div class="pt-1">
          <span class="badge bg-label-secondary">{{ $crecimientoVentas >= 0 ? '+' : '' }}{{ $crecimientoVentas }}%</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Total de Ventas -->
  <div class="mb-4 col-xl-2 col-md-4 col-6">
    <div class="card">
      <div class="card-body">
        <div class="p-2 mb-2 rounded badge bg-label-info"><i class="ti ti-chart-bar ti-md"></i></div>
        <h5 class="pt-2 mb-1 card-title">Total Ventas</h5>
        <small class="text-muted">Última semana</small>
        <p class="mt-1 mb-2">${{ number_format($totalVentasSemana, 2) }}</p>
        <div class="pt-1">
          <span class="badge bg-label-secondary">{{ $crecimientoVentas >= 0 ? '+' : '' }}{{ $crecimientoVentas }}%</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Crecimiento de Ingresos -->
  <div class="mb-4 col-xl-4 col-md-8">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="d-flex flex-column">
            <div class="mb-auto card-title">
              <h5 class="mb-1 text-nowrap">Crecimiento de Ingresos</h5>
              <small>Reporte Semanal</small>
            </div>
            <div class="chart-statistics">
              <h3 class="mb-1 card-title">${{ number_format($totalVentasSemana, 2) }}</h3>
              <span class="badge bg-label-success">{{ $crecimientoVentas >= 0 ? '+' : '' }}{{ $crecimientoVentas }}%</span>
            </div>
          </div>
          <div id="revenueGrowth"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Ventas Diarias -->
  <div class="mb-4 col-xl-8 col-md-12">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <div class="m-0 card-title me-2">
          <h5 class="m-0 me-2">Ventas Diarias</h5>
          <small class="text-muted">Últimos 7 días</small>
        </div>
        <div class="dropdown">
          <button class="p-0 btn" type="button" id="ventasDiariasDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="ti ti-dots-vertical ti-sm text-muted"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="ventasDiariasDropdown">
            <a class="dropdown-item" href="javascript:void(0);">Ver Detalles</a>
            <a class="dropdown-item" href="javascript:void(0);">Exportar</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          @foreach($ventasDiarias as $venta)
          <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card border {{ $venta['es_hoy'] ? 'border-primary' : 'border-light' }} h-100">
              <div class="card-body text-center">
                <div class="mb-2">
                  <span class="badge bg-label-{{ $venta['es_hoy'] ? 'primary' : 'secondary' }} rounded-pill">
                    {{ $venta['dia_semana'] }}
                  </span>
                </div>
                <h6 class="card-title mb-1">{{ $venta['fecha_formateada'] }}</h6>
                <h4 class="mb-1 text-{{ $venta['es_hoy'] ? 'primary' : 'dark' }}">
                  ${{ number_format($venta['total'], 2) }}
                </h4>
                <small class="text-muted">{{ $venta['cantidad_ventas'] }} venta{{ $venta['cantidad_ventas'] != 1 ? 's' : '' }}</small>
                @if($venta['es_hoy'])
                <div class="mt-2">
                  <span class="badge bg-label-primary">Hoy</span>
                </div>
                @endif
              </div>
            </div>
          </div>
          @endforeach
        </div>

        <!-- Resumen de ventas diarias -->
        <div class="row mt-3">
          <div class="col-12">
            <div class="alert alert-info">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-1">Resumen de la Semana</h6>
                  <small>Total de ventas en los últimos 7 días</small>
                </div>
                <div class="text-end">
                  <h5 class="mb-0">${{ number_format(collect($ventasDiarias)->sum('total'), 2) }}</h5>
                  <small>{{ collect($ventasDiarias)->sum('cantidad_ventas') }} ventas</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Reportes de Ganancias -->
  <div class="mb-4 col-12 col-xl-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <div class="mb-0 card-title">
          <h5 class="mb-0">Reportes de Ganancias</h5>
          <small class="text-muted">Vista General Anual</small>
        </div>
        <div class="dropdown">
          <button class="p-0 btn" type="button" id="earningReportsTabsId" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="ti ti-dots-vertical ti-sm text-muted"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="earningReportsTabsId">
            <a class="dropdown-item" href="javascript:void(0);">Ver Más</a>
            <a class="dropdown-item" href="javascript:void(0);">Eliminar</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <ul class="flex-nowrap gap-4 pb-3 mx-1 nav nav-tabs widget-nav-tabs d-flex" role="tablist">
          <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link btn d-flex flex-column align-items-center justify-content-center" role="tab" data-bs-toggle="tab" data-bs-target="#navs-sales-id" aria-controls="navs-sales-id" aria-selected="true">
              <div class="p-2 rounded badge bg-label-secondary"><i class="ti ti-chart-bar ti-sm"></i></div>
              <h6 class="mt-2 mb-0 tab-widget-title">Ventas</h6>
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link btn d-flex flex-column align-items-center justify-content-center" role="tab" data-bs-toggle="tab" data-bs-target="#navs-profit-id" aria-controls="navs-profit-id" aria-selected="false">
              <div class="p-2 rounded badge bg-label-secondary"><i class="ti ti-currency-dollar ti-sm"></i></div>
              <h6 class="mt-2 mb-0 tab-widget-title">Beneficios</h6>
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link btn d-flex flex-column align-items-center justify-content-center" role="tab" data-bs-toggle="tab" data-bs-target="#navs-income-id" aria-controls="navs-income-id" aria-selected="false">
              <div class="p-2 rounded badge bg-label-secondary"><i class="ti ti-chart-pie-2 ti-sm"></i></div>
              <h6 class="mt-2 mb-0 tab-widget-title">Ingresos</h6>
            </a>
          </li>
        </ul>
        <div class="p-0 tab-content ms-0 ms-sm-2">
          <div class="tab-pane fade show active" id="navs-sales-id" role="tabpanel">
            <div id="earningReportsTabsSales"></div>
          </div>
          <div class="tab-pane fade" id="navs-profit-id" role="tabpanel">
            <div id="earningReportsTabsProfit"></div>
          </div>
          <div class="tab-pane fade" id="navs-income-id" role="tabpanel">
            <div id="earningReportsTabsIncome"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Productos Más Vendidos -->
  <div class="mb-4 col-xl-4 col-md-6">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <div class="m-0 card-title me-2">
          <h5 class="m-0 me-2">Productos Más Vendidos</h5>
          <small class="text-muted">Top 5 Productos</small>
        </div>
        <div class="dropdown">
          <button class="p-0 btn" type="button" id="employeeList" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="ti ti-dots-vertical ti-sm text-muted"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="employeeList">
            <a class="dropdown-item" href="javascript:void(0);">Descargar</a>
            <a class="dropdown-item" href="javascript:void(0);">Actualizar</a>
            <a class="dropdown-item" href="javascript:void(0);">Compartir</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <ul class="p-0 m-0">
          @forelse($productosMasVendidos as $index => $producto)
          <li class="pb-1 mb-4 d-flex align-items-center">
            <div class="p-2 rounded badge bg-label-{{ $index % 2 == 0 ? 'primary' : 'success' }} me-3">
              <i class="ti ti-package ti-sm"></i>
            </div>
            <div class="gap-2 d-flex w-100 align-items-center">
              <div class="flex-wrap d-flex justify-content-between flex-grow-1">
                <div>
                  <h6 class="mb-0">{{ $producto->name }}</h6>
                </div>
                <div class="gap-2 user-progress d-flex align-items-center">
                  <h6 class="mb-0">{{ $producto->cantidad_vendida }}</h6>
                </div>
              </div>
              @php
                $maxCantidad = $productosMasVendidos->max('cantidad_vendida');
                $porcentaje = $maxCantidad > 0 ? ($producto->cantidad_vendida / $maxCantidad) * 100 : 0;
              @endphp
              <div class="chart-progress" data-color="{{ $index % 2 == 0 ? 'primary' : 'success' }}" data-series="{{ $porcentaje }}"></div>
            </div>
          </li>
          @empty
          <li class="pb-1 mb-4 d-flex align-items-center">
            <div class="p-2 rounded badge bg-label-secondary me-3">
              <i class="ti ti-package ti-sm"></i>
            </div>
            <div class="gap-2 d-flex w-100 align-items-center">
              <div class="flex-wrap d-flex justify-content-between flex-grow-1">
                <div>
                  <h6 class="mb-0">Sin datos disponibles</h6>
                </div>
                <div class="gap-2 user-progress d-flex align-items-center">
                  <h6 class="mb-0">0</h6>
                </div>
              </div>
              <div class="chart-progress" data-color="secondary" data-series="0"></div>
            </div>
          </li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>

</div>
@endsection
