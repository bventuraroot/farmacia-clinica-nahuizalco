@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Consultas Médicas')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Clínica /</span> Consultas Médicas
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Historial de Consultas</h5>
        <button type="button" class="btn btn-primary" id="btnAddConsultation">
            <i class="fa-solid fa-plus me-1"></i> Nueva Consulta
        </button>
    </div>
    <div class="card-body">
        <div class="alert alert-info" role="alert">
            <h6 class="alert-heading mb-2"><i class="fa-solid fa-notes-medical me-2"></i>Módulo de Consultas</h6>
            <p class="mb-0">Este módulo permite registrar y gestionar las consultas médicas. Incluye:</p>
            <ul class="mb-0 mt-2">
                <li>Registro completo de consultas con signos vitales</li>
                <li>Diagnósticos con códigos CIE-10</li>
                <li>Generación de recetas médicas</li>
                <li>Plan de tratamiento y seguimiento</li>
                <li>Vinculación con expediente del paciente</li>
            </ul>
            <hr>
            <p class="mb-0"><strong>Estado:</strong> <span class="badge bg-success">Módulo Activo</span></p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="consultationsTable">
                <thead>
                    <tr>
                        <th>No. Consulta</th>
                        <th>Fecha/Hora</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Diagnóstico</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center">
                            <i class="fa-solid fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay consultas registradas. Haz clic en "Nueva Consulta" para comenzar.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

