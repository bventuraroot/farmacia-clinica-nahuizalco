<?php

namespace App\Http\Controllers;

use App\Models\MedicalConsultation;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalConsultationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:consultations.index')->only(['index']);
        $this->middleware('permission:consultations.create')->only(['create', 'store']);
        $this->middleware('permission:consultations.edit')->only(['edit', 'update']);
        $this->middleware('permission:consultations.show')->only(['show']);
    }

    /**
     * Display a listing of consultations
     */
    public function index()
    {
        $user = Auth::user();
        $company_id = $user->company_id ?? Company::first()->id;

        return view('clinic.consultations.index', compact('company_id'));
    }

    /**
     * Get consultations data
     */
    public function getConsultations(Request $request)
    {
        $user = Auth::user();
        $company_id = $request->get('company_id', $user->company_id ?? Company::first()->id);

        $consultations = MedicalConsultation::with(['patient', 'doctor', 'appointment'])
            ->where('company_id', $company_id)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('numero_consulta', 'like', "%{$search}%")
                        ->orWhereHas('patient', function ($q) use ($search) {
                            $q->where('primer_nombre', 'like', "%{$search}%")
                                ->orWhere('primer_apellido', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('fecha_hora', 'desc')
            ->paginate(15);

        return response()->json($consultations);
    }

    /**
     * Show the form for creating a new consultation
     */
    public function create(Request $request)
    {
        $appointment_id = $request->get('appointment_id');
        $appointment = $appointment_id ? Appointment::with(['patient', 'doctor'])->findOrFail($appointment_id) : null;
        
        $patients = Patient::where('estado', 'activo')->get();
        $doctors = Doctor::where('estado', 'activo')->get();
        
        return view('clinic.consultations.create', compact('appointment', 'patients', 'doctors'));
    }

    /**
     * Store a newly created consultation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'appointment_id' => 'nullable|exists:appointments,id',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'motivo_consulta' => 'required|string',
            'sintomas' => 'nullable|string',
            
            // Signos vitales
            'temperatura' => 'nullable|numeric|min:30|max:45',
            'presion_arterial' => 'nullable|string|max:20',
            'frecuencia_cardiaca' => 'nullable|integer|min:30|max:200',
            'frecuencia_respiratoria' => 'nullable|integer|min:5|max:60',
            'peso' => 'nullable|numeric|min:0|max:500',
            'altura' => 'nullable|numeric|min:0|max:300',
            'saturacion_oxigeno' => 'nullable|integer|min:50|max:100',
            
            // Diagnóstico
            'exploracion_fisica' => 'nullable|string',
            'diagnostico_cie10' => 'nullable|string|max:20',
            'diagnostico_descripcion' => 'required|string',
            'diagnosticos_secundarios' => 'nullable|string',
            'plan_tratamiento' => 'nullable|string',
            'indicaciones' => 'nullable|string',
            'observaciones' => 'nullable|string',
            
            // Receta y seguimiento
            'genera_receta' => 'boolean',
            'receta_digital' => 'nullable|string',
            'requiere_seguimiento' => 'boolean',
            'fecha_proximo_control' => 'nullable|date',
        ]);

        $user = Auth::user();
        $validated['company_id'] = $request->company_id ?? $user->company_id ?? Company::first()->id;
        $validated['fecha_hora'] = now();
        
        // Generar número de consulta único
        $validated['numero_consulta'] = 'CONS-' . now()->format('Ymd') . '-' . str_pad(MedicalConsultation::count() + 1, 5, '0', STR_PAD_LEFT);
        
        // Calcular IMC si hay peso y altura
        if (isset($validated['peso']) && isset($validated['altura']) && $validated['altura'] > 0) {
            $altura_metros = $validated['altura'] / 100;
            $validated['imc'] = round($validated['peso'] / ($altura_metros * $altura_metros), 2);
        }

        $consultation = MedicalConsultation::create($validated);

        // Si viene de una cita, actualizar el estado de la cita
        if ($validated['appointment_id']) {
            Appointment::find($validated['appointment_id'])->update(['estado' => 'completada']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Consulta creada exitosamente',
            'consultation' => $consultation->load(['patient', 'doctor'])
        ]);
    }

    /**
     * Display the specified consultation
     */
    public function show($id)
    {
        $consultation = MedicalConsultation::with(['patient', 'doctor', 'appointment', 'prescriptions', 'labOrders'])->findOrFail($id);
        
        return view('clinic.consultations.show', compact('consultation'));
    }

    /**
     * Update the specified consultation
     */
    public function update(Request $request, $id)
    {
        $consultation = MedicalConsultation::findOrFail($id);

        $validated = $request->validate([
            'motivo_consulta' => 'required|string',
            'sintomas' => 'nullable|string',
            'temperatura' => 'nullable|numeric|min:30|max:45',
            'presion_arterial' => 'nullable|string|max:20',
            'frecuencia_cardiaca' => 'nullable|integer|min:30|max:200',
            'frecuencia_respiratoria' => 'nullable|integer|min:5|max:60',
            'peso' => 'nullable|numeric|min:0|max:500',
            'altura' => 'nullable|numeric|min:0|max:300',
            'saturacion_oxigeno' => 'nullable|integer|min:50|max:100',
            'exploracion_fisica' => 'nullable|string',
            'diagnostico_cie10' => 'nullable|string|max:20',
            'diagnostico_descripcion' => 'required|string',
            'diagnosticos_secundarios' => 'nullable|string',
            'plan_tratamiento' => 'nullable|string',
            'indicaciones' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'genera_receta' => 'boolean',
            'receta_digital' => 'nullable|string',
            'requiere_seguimiento' => 'boolean',
            'fecha_proximo_control' => 'nullable|date',
            'estado' => 'required|in:en_curso,finalizada,cancelada',
        ]);

        // Recalcular IMC si hay peso y altura
        if (isset($validated['peso']) && isset($validated['altura']) && $validated['altura'] > 0) {
            $altura_metros = $validated['altura'] / 100;
            $validated['imc'] = round($validated['peso'] / ($altura_metros * $altura_metros), 2);
        }

        $consultation->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Consulta actualizada exitosamente',
            'consultation' => $consultation
        ]);
    }

    /**
     * Finalizar consulta
     */
    public function finalize($id)
    {
        $consultation = MedicalConsultation::findOrFail($id);
        
        $consultation->update(['estado' => 'finalizada']);

        return response()->json([
            'success' => true,
            'message' => 'Consulta finalizada exitosamente'
        ]);
    }

    /**
     * Get patient medical history
     */
    public function patientHistory($patient_id)
    {
        $consultations = MedicalConsultation::with(['doctor', 'prescriptions', 'labOrders'])
            ->where('patient_id', $patient_id)
            ->where('estado', 'finalizada')
            ->orderBy('fecha_hora', 'desc')
            ->get();

        return response()->json($consultations);
    }
}

