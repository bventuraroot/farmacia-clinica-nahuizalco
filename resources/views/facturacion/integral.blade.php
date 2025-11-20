@extends('layouts/layoutMaster')

@section('title', 'Facturación Integral - Todos los Módulos')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<style>
.servicio-card {
    transition: all 0.2s;
    cursor: pointer;
    border: 2px solid transparent;
}
.servicio-card:hover {
    border-color: #696cff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.servicio-card.selected {
    border-color: #696cff;
    background-color: #f5f5ff;
}
</style>
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
@endsection

@section('content')

<!-- Header -->
<div class="row mb-4">
    <div class="col-md-8">
        <h4 class="fw-bold py-3 mb-2">
            <span class="text-muted fw-light"><a href="/dashboard" class="text-muted"><i class="fa-solid fa-arrow-left me-2"></i>Dashboard</a> /</span> 
            Facturación Integral
        </h4>
        <p class="text-muted">Factura servicios de Farmacia y Laboratorio desde un solo lugar. La clínica es solo para control de citas, pacientes y consultas médicas.</p>
    </div>
    <div class="col-md-4 text-end">
        <div class="card bg-primary text-white">
            <div class="card-body p-3">
                <h6 class="text-white mb-1">Total Facturado Hoy</h6>
                <h3 class="text-white mb-0">${{ number_format($totalHoy, 2) }}</h3>
                <small class="text-white-50">{{ $ventasHoy }} facturas emitidas</small>
            </div>
        </div>
    </div>
</div>

<!-- Pestañas de Facturación -->
<div class="nav-align-top mb-4">
    <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
        <li class="nav-item">
            <button type="button" class="nav-link {{ $tipo == 'farmacia' ? 'active' : '' }}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-farmacia" aria-controls="navs-farmacia">
                <i class="fa-solid fa-pills tf-icons me-2"></i>
                Farmacia
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link {{ $tipo == 'laboratorio' ? 'active' : '' }}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-laboratorio" aria-controls="navs-laboratorio">
                <i class="fa-solid fa-flask tf-icons me-2"></i>
                Órdenes de Laboratorio
                @if(count($ordenesLabPorFacturar) > 0)
                <span class="badge rounded-pill badge-center bg-danger ms-2">{{ count($ordenesLabPorFacturar) }}</span>
                @endif
            </button>
        </li>
    </ul>

    <div class="tab-content">
        
        <!-- TAB FARMACIA -->
        <div class="tab-pane fade {{ $tipo == 'farmacia' ? 'show active' : '' }}" id="navs-farmacia" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Venta de Productos - Farmacia</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Para realizar ventas de productos de farmacia, utiliza el módulo de ventas estándar.
                    </div>
                    <div class="d-flex gap-2">
                        <a href="/sale/create-dynamic" class="btn btn-primary">
                            <i class="fa-solid fa-cash-register me-2"></i>Nueva Venta de Productos
                        </a>
                        <a href="/sale/index" class="btn btn-outline-primary">
                            <i class="fa-solid fa-list me-2"></i>Ver Todas las Ventas
                        </a>
                        <a href="/presales/index" class="btn btn-outline-success">
                            <i class="fa-solid fa-cart-shopping me-2"></i>Ventas Menudeo
                        </a>
                    </div>
                </div>
            </div>
        </div>


        <!-- TAB ÓRDENES DE LABORATORIO -->
        <div class="tab-pane fade {{ $tipo == 'laboratorio' ? 'show active' : '' }}" id="navs-laboratorio" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Órdenes de Laboratorio por Facturar</h5>
                        <small class="text-muted">Órdenes completadas pendientes de facturación</small>
                    </div>
                    <a href="/lab-orders" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-list me-1"></i>Ver Todas las Órdenes
                    </a>
                </div>
                <div class="card-body">
                    @if(count($ordenesLabPorFacturar) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No. Orden</th>
                                    <th>Fecha</th>
                                    <th>Paciente</th>
                                    <th>Exámenes</th>
                                    <th>Estado</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ordenesLabPorFacturar as $orden)
                                <tr>
                                    <td>
                                        <strong>{{ $orden->numero_orden }}</strong>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($orden->fecha_orden)->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-warning">
                                                    {{ substr($orden->patient->primer_nombre, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $orden->patient->nombre_completo }}</div>
                                                <small class="text-muted">{{ $orden->patient->documento_identidad }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-info">{{ $orden->exams->count() }} examen(es)</span>
                                        <br>
                                        <small class="text-muted">
                                            @foreach($orden->exams->take(2) as $exam)
                                                {{ $exam->exam->nombre }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                            @if($orden->exams->count() > 2)
                                                ...
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-success">Completada</span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-warning">${{ number_format($orden->total, 2) }}</strong>
                                        <br>
                                        <small class="text-muted">Exámenes</small>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning" onclick="facturarOrdenLab({{ $orden->id }})">
                                            <i class="fa-solid fa-file-invoice me-1"></i>Facturar
                                        </button>
                                        <a href="/lab-orders/{{ $orden->id }}" class="btn btn-sm btn-outline-info" target="_blank">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fa-solid fa-check-circle fa-4x text-success mb-3 d-block"></i>
                        <h5>No hay órdenes pendientes de facturar</h5>
                        <p class="text-muted">Todas las órdenes completadas han sido facturadas</p>
                        <a href="/lab-orders/create" class="btn btn-warning mt-3">
                            <i class="fa-solid fa-plus me-2"></i>Nueva Orden de Laboratorio
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Estadísticas Rápidas -->
<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Productos Farmacia</h6>
                        <h4 class="mb-0">-</h4>
                        <small class="text-muted">Usar módulo de ventas</small>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="fa-solid fa-pills fa-2x"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Órdenes Lab. Pendientes</h6>
                        <h4 class="mb-0 text-warning">{{ count($ordenesLabPorFacturar) }}</h4>
                        <small class="text-muted">Por facturar</small>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="fa-solid fa-flask fa-2x"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
<script>
// Las consultas médicas NO se facturan - solo control clínico

function facturarOrdenLab(ordenId) {
    if (confirm('¿Desea facturar esta orden de laboratorio?')) {
        // Aquí implementar la lógica de facturación
        fetch(`/facturacion/orden-lab/${ordenId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Orden de laboratorio facturada exitosamente');
                window.location.reload();
            }
        });
    }
}
</script>
@endsection

