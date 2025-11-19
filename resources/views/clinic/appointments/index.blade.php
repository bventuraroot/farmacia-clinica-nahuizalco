@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Agenda de Citas')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/fullcalendar/fullcalendar.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/fullcalendar/fullcalendar.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Clínica /</span> Citas Médicas
</h4>

<div class="row">
    <div class="col-lg-3 col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <button type="button" class="btn btn-primary w-100 mb-3" id="btnAddAppointment">
                    <i class="fa-solid fa-plus me-1"></i> Nueva Cita
                </button>
                <div class="alert alert-info mb-0" role="alert">
                    <h6 class="alert-heading"><i class="fa-solid fa-calendar-check me-2"></i>Agenda de Citas</h6>
                    <p class="small mb-0">Gestiona las citas médicas de manera eficiente.</p>
                    <hr class="my-2">
                    <div class="small">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-primary me-2" style="width: 15px; height: 15px;"></span>
                            <span>Programada</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-success me-2" style="width: 15px; height: 15px;"></span>
                            <span>Confirmada</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-warning me-2" style="width: 15px; height: 15px;"></span>
                            <span>En Curso</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-danger me-2" style="width: 15px; height: 15px;"></span>
                            <span>Cancelada</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9 col-md-12">
        <div class="card">
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>
@endsection

