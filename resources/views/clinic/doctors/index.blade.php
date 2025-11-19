@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Gestión de Médicos')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Clínica /</span> Médicos
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Personal Médico</h5>
        <button type="button" class="btn btn-primary" id="btnAddDoctor">
            <i class="fa-solid fa-plus me-1"></i> Nuevo Médico
        </button>
    </div>
    <div class="card-body">
        <div class="alert alert-info" role="alert">
            <h6 class="alert-heading mb-2"><i class="fa-solid fa-user-md me-2"></i>Módulo de Médicos</h6>
            <p class="mb-0">Este módulo permite gestionar el personal médico de la clínica. Funcionalidades:</p>
            <ul class="mb-0 mt-2">
                <li>Registrar médicos con su información profesional (JVPM, especialidades)</li>
                <li>Gestionar horarios de atención</li>
                <li>Ver agenda de citas por médico</li>
                <li>Vincular con usuarios del sistema para acceso a la plataforma</li>
            </ul>
            <hr>
            <p class="mb-0"><strong>Estado:</strong> <span class="badge bg-success">Módulo Activo</span></p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="doctorsTable">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>JVPM</th>
                        <th>Especialidad</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center">
                            <i class="fa-solid fa-stethoscope fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay médicos registrados. Haz clic en "Nuevo Médico" para comenzar.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

