<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:patients.index')->only(['index']);
        $this->middleware('permission:patients.create')->only(['create', 'store']);
        $this->middleware('permission:patients.edit')->only(['edit', 'update']);
        $this->middleware('permission:patients.destroy')->only(['destroy']);
        $this->middleware('permission:patients.show')->only(['show']);
    }

    /**
     * Display a listing of patients
     */
    public function index()
    {
        $user = Auth::user();
        $company_id = $user->company_id ?? Company::first()->id;

        return view('clinic.patients.index', compact('company_id'));
    }

    /**
     * Get patients data for datatables
     */
    public function getPatients(Request $request)
    {
        $user = Auth::user();
        $company_id = $request->get('company_id', $user->company_id ?? Company::first()->id);

        $patients = Patient::with('company')
            ->where('company_id', $company_id)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('codigo_paciente', 'like', "%{$search}%")
                        ->orWhere('numero_expediente', 'like', "%{$search}%")
                        ->orWhere('documento_identidad', 'like', "%{$search}%")
                        ->orWhere('primer_nombre', 'like', "%{$search}%")
                        ->orWhere('primer_apellido', 'like', "%{$search}%")
                        ->orWhere('telefono', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($patients);
    }

    /**
     * Show the form for creating a new patient
     */
    public function create()
    {
        return view('clinic.patients.create');
    }

    /**
     * Store a newly created patient
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'primer_nombre' => 'required|string|max:100',
            'segundo_nombre' => 'nullable|string|max:100',
            'primer_apellido' => 'required|string|max:100',
            'segundo_apellido' => 'nullable|string|max:100',
            'documento_identidad' => 'required|string|max:50|unique:patients',
            'tipo_documento' => 'required|in:DUI,NIT,Pasaporte,Carnet_residente',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|in:M,F',
            'telefono' => 'required|string|max:20',
            'telefono_emergencia' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'direccion' => 'required|string',
            'tipo_sangre' => 'nullable|string|max:10',
            'alergias' => 'nullable|string',
            'enfermedades_cronicas' => 'nullable|string',
        ]);

        $user = Auth::user();
        $validated['company_id'] = $request->company_id ?? $user->company_id ?? Company::first()->id;
        
        // Generar código de paciente único
        $validated['codigo_paciente'] = 'PAC-' . strtoupper(uniqid());
        
        // Generar número de expediente único
        $validated['numero_expediente'] = 'EXP-' . now()->format('Ymd') . '-' . str_pad(Patient::count() + 1, 5, '0', STR_PAD_LEFT);

        $patient = Patient::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Paciente creado exitosamente',
            'patient' => $patient
        ]);
    }

    /**
     * Display the specified patient
     */
    public function show($id)
    {
        $patient = Patient::with(['appointments', 'consultations', 'labOrders'])->findOrFail($id);
        
        return view('clinic.patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified patient
     */
    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        
        return view('clinic.patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient
     */
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $validated = $request->validate([
            'primer_nombre' => 'required|string|max:100',
            'segundo_nombre' => 'nullable|string|max:100',
            'primer_apellido' => 'required|string|max:100',
            'segundo_apellido' => 'nullable|string|max:100',
            'documento_identidad' => 'required|string|max:50|unique:patients,documento_identidad,' . $id,
            'tipo_documento' => 'required|in:DUI,NIT,Pasaporte,Carnet_residente',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|in:M,F',
            'telefono' => 'required|string|max:20',
            'telefono_emergencia' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'direccion' => 'required|string',
            'tipo_sangre' => 'nullable|string|max:10',
            'alergias' => 'nullable|string',
            'enfermedades_cronicas' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $patient->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Paciente actualizado exitosamente',
            'patient' => $patient
        ]);
    }

    /**
     * Remove the specified patient
     */
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();

        return response()->json([
            'success' => true,
            'message' => 'Paciente eliminado exitosamente'
        ]);
    }

    /**
     * Get patient by document ID for quick search
     */
    public function searchByDocument(Request $request)
    {
        $document = $request->get('document');
        
        $patient = Patient::where('documento_identidad', $document)->first();

        if ($patient) {
            return response()->json([
                'success' => true,
                'patient' => $patient
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Paciente no encontrado'
        ], 404);
    }
}

