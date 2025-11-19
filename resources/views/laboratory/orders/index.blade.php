@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Órdenes de Laboratorio')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Laboratorio /</span> Órdenes de Laboratorio
</h4>

<div class="row mb-4">
    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold d-block mb-1">Pendientes</span>
                        <h3 class="card-title mb-0">0</h3>
                    </div>
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="fa-solid fa-hourglass-half fa-2x"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold d-block mb-1">En Proceso</span>
                        <h3 class="card-title mb-0">0</h3>
                    </div>
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="fa-solid fa-spinner fa-2x"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold d-block mb-1">Completadas Hoy</span>
                        <h3 class="card-title mb-0">0</h3>
                    </div>
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="fa-solid fa-check-circle fa-2x"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold d-block mb-1">Total del Mes</span>
                        <h3 class="card-title mb-0">0</h3>
                    </div>
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="fa-solid fa-flask fa-2x"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Órdenes de Laboratorio</h5>
        <button type="button" class="btn btn-primary" id="btnAddOrder">
            <i class="fa-solid fa-plus me-1"></i> Nueva Orden
        </button>
    </div>
    <div class="card-body">
        <div class="alert alert-info" role="alert">
            <h6 class="alert-heading mb-2"><i class="fa-solid fa-flask me-2"></i>Módulo de Laboratorio</h6>
            <p class="mb-0">Este módulo permite gestionar las órdenes de laboratorio clínico. Funcionalidades:</p>
            <ul class="mb-0 mt-2">
                <li>Crear órdenes de exámenes de laboratorio</li>
                <li>Gestionar toma de muestras</li>
                <li>Registrar y validar resultados</li>
                <li>Generar reportes de resultados</li>
                <li>Control de calidad y equipamiento</li>
            </ul>
            <hr>
            <p class="mb-0"><strong>Estado:</strong> <span class="badge bg-success">Módulo Activo</span></p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="ordersTable">
                <thead>
                    <tr>
                        <th>No. Orden</th>
                        <th>Fecha</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Exámenes</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center">
                            <i class="fa-solid fa-vial fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay órdenes de laboratorio. Haz clic en "Nueva Orden" para comenzar.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
$(document).ready(function() {
    // Botón para agregar nueva orden
    $('#btnAddOrder').on('click', function() {
        window.location.href = '/lab-orders/create';
    });

    // Cargar estadísticas
    cargarEstadisticas();
    
    // Cargar lista de órdenes
    cargarOrdenes();
});

function cargarEstadisticas() {
    $.ajax({
        url: '/lab-orders/pending-count',
        method: 'GET',
        success: function(response) {
            // Actualizar contadores si la respuesta tiene datos
            if (response.pendientes !== undefined) {
                $('.card:eq(0) .card-title').text(response.pendientes || 0);
            }
            if (response.en_proceso !== undefined) {
                $('.card:eq(1) .card-title').text(response.en_proceso || 0);
            }
            if (response.completadas_hoy !== undefined) {
                $('.card:eq(2) .card-title').text(response.completadas_hoy || 0);
            }
            if (response.total_mes !== undefined) {
                $('.card:eq(3) .card-title').text(response.total_mes || 0);
            }
        }
    });
}

function cargarOrdenes() {
    $.ajax({
        url: '/lab-orders/data',
        method: 'GET',
        success: function(response) {
            let html = '';
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(orden => {
                    const fechaOrden = new Date(orden.fecha_orden);
                    const fechaFormateada = fechaOrden.toLocaleDateString('es-ES', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });
                    
                    html += `
                        <tr>
                            <td><code>${orden.numero_orden}</code></td>
                            <td>${fechaFormateada}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            ${orden.patient ? orden.patient.primer_nombre.charAt(0) : '?'}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">${orden.patient ? orden.patient.nombre_completo : 'N/A'}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                ${orden.doctor ? orden.doctor.nombre_completo : 'Sin médico'}
                            </td>
                            <td>
                                <span class="badge bg-label-info">${orden.exams ? orden.exams.length : 0} exámenes</span>
                                <br><small class="text-muted">$${parseFloat(orden.total || 0).toFixed(2)}</small>
                            </td>
                            <td>
                                <span class="badge bg-label-${
                                    orden.estado === 'completada' ? 'success' :
                                    (orden.estado === 'en_proceso' ? 'info' :
                                    (orden.estado === 'cancelada' ? 'danger' : 'warning'))
                                }">
                                    ${orden.estado ? orden.estado.replace('_', ' ').charAt(0).toUpperCase() + orden.estado.replace('_', ' ').slice(1) : 'Pendiente'}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/lab-orders/${orden.id}" class="btn btn-sm btn-outline-info">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="/lab-orders/${orden.id}/print" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `;
                });
            } else {
                html = `
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fa-solid fa-vial fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted mb-3">No hay órdenes de laboratorio registradas</p>
                            <button class="btn btn-primary" onclick="window.location.href='/lab-orders/create'">
                                <i class="fa-solid fa-plus me-1"></i>Crear Primera Orden
                            </button>
                        </td>
                    </tr>
                `;
            }
            
            $('#ordersTable tbody').html(html);
        },
        error: function(xhr) {
            console.error('Error al cargar órdenes:', xhr);
        }
    });
}
</script>
@endsection

