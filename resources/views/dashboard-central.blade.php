@extends('layouts/layoutMaster')

@section('title', 'Centro de Control - Sistema Integral')

@section('vendor-style')
<style>
.module-card {
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid transparent;
    height: 100%;
    min-height: 280px;
}
.module-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}
.module-card.farmacia:hover {
    border-color: #696cff;
}
.module-card.clinica:hover {
    border-color: #71dd37;
}
.module-card.laboratorio:hover {
    border-color: #ffab00;
}
.module-card.facturacion:hover {
    border-color: #03c3ec;
}
.module-icon {
    width: 120px;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 20px;
    margin: 0 auto 20px;
}
.stat-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    min-width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 18px;
    font-weight: bold;
}
.quick-action-btn {
    width: 100%;
    margin-bottom: 10px;
    text-align: left;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 0;
    margin: 10px -25px 30px -25px;
    border-radius: 0;
}
</style>
@endsection

@section('content')

<!-- Header Principal -->
<div class="dashboard-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="text-white mb-2">¡Bienvenido, {{ auth()->user()->name }}!</h2>
                <p class="text-white-50 mb-0">
                    <i class="fa-solid fa-calendar-day me-2"></i>{{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                </p>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex justify-content-end gap-2">
                    <div class="text-center">
                        <h4 class="text-white mb-0">{{ \Carbon\Carbon::now()->format('H:i') }}</h4>
                        <small class="text-white-50">Hora Actual</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alertas Importantes -->
@if(($alertas['stockBajo'] ?? 0) > 0 || ($alertas['proximosVencer'] ?? 0) > 0 || ($alertas['citasPendientes'] ?? 0) > 0 || ($alertas['ordenesPendientes'] ?? 0) > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning alert-dismissible d-flex align-items-center" role="alert">
            <i class="fa-solid fa-bell fa-2x me-3"></i>
            <div class="flex-grow-1">
                <h6 class="alert-heading mb-2">Atención Requerida</h6>
                <div class="row">
                    @if(($alertas['stockBajo'] ?? 0) > 0)
                    <div class="col-md-3 mb-2">
                        <span class="badge bg-danger me-1">{{ $alertas['stockBajo'] }}</span>
                        <span>Productos con stock bajo</span>
                    </div>
                    @endif
                    @if(($alertas['proximosVencer'] ?? 0) > 0)
                    <div class="col-md-3 mb-2">
                        <span class="badge bg-warning me-1">{{ $alertas['proximosVencer'] }}</span>
                        <span>Productos por vencer</span>
                    </div>
                    @endif
                    @if(($alertas['citasPendientes'] ?? 0) > 0)
                    <div class="col-md-3 mb-2">
                        <span class="badge bg-info me-1">{{ $alertas['citasPendientes'] }}</span>
                        <span>Citas pendientes hoy</span>
                    </div>
                    @endif
                    @if(($alertas['ordenesPendientes'] ?? 0) > 0)
                    <div class="col-md-3 mb-2">
                        <span class="badge bg-primary me-1">{{ $alertas['ordenesPendientes'] }}</span>
                        <span>Órdenes de lab. pendientes</span>
                    </div>
                    @endif
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

<!-- Título -->
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold">Selecciona el Módulo donde Deseas Trabajar</h4>
        <p class="text-muted">Cada módulo está diseñado para concentrarte en una actividad específica</p>
    </div>
</div>

<!-- Módulos Principales -->
<div class="row g-4 mb-4">
    
    <!-- MÓDULO FARMACIA -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card module-card farmacia" onclick="window.location.href='/dashboard-farmacia'">
            <div class="card-body text-center position-relative">
                @if(($alertas['stockBajo'] ?? 0) > 0 || ($alertas['proximosVencer'] ?? 0) > 0)
                <span class="stat-badge bg-label-danger">
                    <i class="fa-solid fa-exclamation"></i>
                </span>
                @endif
                
                <div class="module-icon bg-label-primary">
                    <i class="fa-solid fa-pills fa-4x text-primary"></i>
                </div>
                
                <h3 class="mb-2">Farmacia</h3>
                <p class="text-muted mb-4">Gestión de medicamentos, ventas e inventario</p>
                
                <div class="row text-start mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Ventas Hoy</small>
                        <h5 class="mb-0 text-primary">${{ number_format($totalVentasHoy ?? 0, 2) }}</h5>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Productos</small>
                        <h5 class="mb-0">{{ $tproducts ?? 0 }}</h5>
                    </div>
                </div>
                
                <div class="d-grid">
                    <button class="btn btn-primary">
                        <i class="fa-solid fa-arrow-right me-2"></i>Acceder a Farmacia
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MÓDULO CLÍNICA -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card module-card clinica" onclick="window.location.href='/dashboard-clinica'">
            <div class="card-body text-center position-relative">
                @if(($alertas['citasPendientes'] ?? 0) > 0)
                <span class="stat-badge bg-label-warning">
                    {{ $alertas['citasPendientes'] }}
                </span>
                @endif
                
                <div class="module-icon bg-label-success">
                    <i class="fa-solid fa-stethoscope fa-4x text-success"></i>
                </div>
                
                <h3 class="mb-2">Clínica Médica</h3>
                <p class="text-muted mb-4">Atención médica, citas y consultas</p>
                
                <div class="row text-start mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Citas Hoy</small>
                        <h5 class="mb-0 text-success">{{ $citasHoy ?? 0 }}</h5>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Pacientes</small>
                        <h5 class="mb-0">{{ $tpacientes ?? 0 }}</h5>
                    </div>
                </div>
                
                <div class="d-grid">
                    <button class="btn btn-success">
                        <i class="fa-solid fa-arrow-right me-2"></i>Acceder a Clínica
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MÓDULO LABORATORIO -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card module-card laboratorio" onclick="window.location.href='/dashboard-laboratorio'">
            <div class="card-body text-center position-relative">
                @if(($alertas['ordenesPendientes'] ?? 0) > 0)
                <span class="stat-badge bg-label-warning">
                    {{ $alertas['ordenesPendientes'] }}
                </span>
                @endif
                
                <div class="module-icon bg-label-warning">
                    <i class="fa-solid fa-flask fa-4x text-warning"></i>
                </div>
                
                <h3 class="mb-2">Laboratorio</h3>
                <p class="text-muted mb-4">Exámenes clínicos y resultados</p>
                
                <div class="row text-start mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Órdenes Hoy</small>
                        <h5 class="mb-0 text-warning">{{ $ordenesLabHoy ?? 0 }}</h5>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Pendientes</small>
                        <h5 class="mb-0">{{ $ordenesPendientes ?? 0 }}</h5>
                    </div>
                </div>
                
                <div class="d-grid">
                    <button class="btn btn-warning">
                        <i class="fa-solid fa-arrow-right me-2"></i>Acceder a Laboratorio
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MÓDULO FACTURACIÓN -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card module-card facturacion" onclick="window.location.href='/facturacion-integral'">
            <div class="card-body text-center position-relative">
                @php
                    $pendientesFacturar = ($ordenesPendientes ?? 0) + ($citasPendientesHoy ?? 0);
                @endphp
                @if($pendientesFacturar > 0)
                <span class="stat-badge bg-label-info">
                    {{ $pendientesFacturar }}
                </span>
                @endif
                
                <div class="module-icon bg-label-info">
                    <i class="fa-solid fa-file-invoice-dollar fa-4x text-info"></i>
                </div>
                
                <h3 class="mb-2">Facturación</h3>
                <p class="text-muted mb-4">Factura servicios de todos los módulos</p>
                
                <div class="row text-start mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Por Facturar</small>
                        <h5 class="mb-0 text-info">{{ $pendientesFacturar }}</h5>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Hoy</small>
                        <h5 class="mb-0">${{ number_format($totalVentasHoy ?? 0, 2) }}</h5>
                    </div>
                </div>
                
                <div class="d-grid">
                    <button class="btn btn-info">
                        <i class="fa-solid fa-arrow-right me-2"></i>Acceder a Facturación
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resumen Rápido -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Resumen del Día</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3">
                            <i class="fa-solid fa-dollar-sign fa-2x text-primary mb-2"></i>
                            <h4 class="mb-0">${{ number_format(($totalVentasHoy ?? 0), 2) }}</h4>
                            <small class="text-muted">Total Facturado Hoy</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3">
                            <i class="fa-solid fa-calendar-check fa-2x text-success mb-2"></i>
                            <h4 class="mb-0">{{ ($citasHoy ?? 0) + ($consultasHoy ?? 0) }}</h4>
                            <small class="text-muted">Atenciones Médicas Hoy</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3">
                            <i class="fa-solid fa-vial fa-2x text-warning mb-2"></i>
                            <h4 class="mb-0">{{ $ordenesLabHoy ?? 0 }}</h4>
                            <small class="text-muted">Exámenes Solicitados Hoy</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3">
                            <i class="fa-solid fa-users fa-2x text-info mb-2"></i>
                            <h4 class="mb-0">{{ ($tclientes ?? 0) + ($tpacientes ?? 0) }}</h4>
                            <small class="text-muted">Clientes/Pacientes Total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Accesos Rápidos -->
<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-header bg-label-primary">
                <h6 class="mb-0"><i class="fa-solid fa-pills me-2"></i>Farmacia</h6>
            </div>
            <div class="card-body">
                <a href="/sale/create-dynamic" class="quick-action-btn btn btn-sm btn-outline-primary">
                    <span><i class="fa-solid fa-cash-register me-2"></i>Nueva Venta</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="/products" class="quick-action-btn btn btn-sm btn-outline-primary">
                    <span><i class="fa-solid fa-box me-2"></i>Inventario</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="/purchase/index" class="quick-action-btn btn btn-sm btn-outline-primary">
                    <span><i class="fa-solid fa-truck me-2"></i>Compras</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-header bg-label-success">
                <h6 class="mb-0"><i class="fa-solid fa-stethoscope me-2"></i>Clínica</h6>
            </div>
            <div class="card-body">
                <a href="/appointments/create" class="quick-action-btn btn btn-sm btn-outline-success">
                    <span><i class="fa-solid fa-calendar-plus me-2"></i>Nueva Cita</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="/consultations/create" class="quick-action-btn btn btn-sm btn-outline-success">
                    <span><i class="fa-solid fa-notes-medical me-2"></i>Nueva Consulta</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="/patients" class="quick-action-btn btn btn-sm btn-outline-success">
                    <span><i class="fa-solid fa-user-injured me-2"></i>Pacientes</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-header bg-label-warning">
                <h6 class="mb-0"><i class="fa-solid fa-flask me-2"></i>Laboratorio</h6>
            </div>
            <div class="card-body">
                <a href="/lab-orders/create" class="quick-action-btn btn btn-sm btn-outline-warning">
                    <span><i class="fa-solid fa-plus me-2"></i>Nueva Orden</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="/lab-orders?estado=pendiente" class="quick-action-btn btn btn-sm btn-outline-warning">
                    <span><i class="fa-solid fa-hourglass me-2"></i>Pendientes</span>
                    <span class="badge bg-danger">{{ $ordenesPendientes ?? 0 }}</span>
                </a>
                <a href="/lab-orders" class="quick-action-btn btn btn-sm btn-outline-warning">
                    <span><i class="fa-solid fa-list me-2"></i>Todas las Órdenes</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-header bg-label-info">
                <h6 class="mb-0"><i class="fa-solid fa-file-invoice me-2"></i>Facturación</h6>
            </div>
            <div class="card-body">
                <a href="/facturacion-integral?tipo=farmacia" class="quick-action-btn btn btn-sm btn-outline-info">
                    <span><i class="fa-solid fa-pills me-2"></i>Facturar Farmacia</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="/facturacion-integral?tipo=clinica" class="quick-action-btn btn btn-sm btn-outline-info">
                    <span><i class="fa-solid fa-stethoscope me-2"></i>Facturar Clínica</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="/facturacion-integral?tipo=laboratorio" class="quick-action-btn btn btn-sm btn-outline-info">
                    <span><i class="fa-solid fa-flask me-2"></i>Facturar Laboratorio</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

