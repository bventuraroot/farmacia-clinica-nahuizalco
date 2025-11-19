@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Gestión de Pacientes')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
@endsection

@section('page-script')
<script>
$(document).ready(function() {
    // Placeholder para la funcionalidad de pacientes
    console.log('Módulo de Pacientes cargado');
});
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Clínica /</span> Pacientes
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Pacientes</h5>
        <button type="button" class="btn btn-primary" id="btnAddPatient">
            <i class="fa-solid fa-plus me-1"></i> Nuevo Paciente
        </button>
    </div>
    <div class="card-body">
        <div class="alert alert-info" role="alert">
            <h6 class="alert-heading mb-2"><i class="fa-solid fa-info-circle me-2"></i>Módulo de Pacientes</h6>
            <p class="mb-0">Este módulo permite gestionar la información de los pacientes de la clínica. Aquí podrás:</p>
            <ul class="mb-0 mt-2">
                <li>Registrar nuevos pacientes con su información personal y médica</li>
                <li>Ver el expediente clínico completo de cada paciente</li>
                <li>Gestionar el historial de consultas y tratamientos</li>
                <li>Vincular pacientes con citas médicas y órdenes de laboratorio</li>
            </ul>
            <hr>
            <p class="mb-0"><strong>Estado:</strong> <span class="badge bg-success">Módulo Activo</span></p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="patientsTable">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Expediente</th>
                        <th>Nombre Completo</th>
                        <th>Documento</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center">
                            <i class="fa-solid fa-user-plus fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay pacientes registrados. Haz clic en "Nuevo Paciente" para comenzar.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

