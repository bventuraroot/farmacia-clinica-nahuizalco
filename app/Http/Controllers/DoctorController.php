<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:doctors.index')->only(['index']);
        $this->middleware('permission:doctors.create')->only(['create', 'store']);
        $this->middleware('permission:doctors.edit')->only(['edit', 'update']);
        $this->middleware('permission:doctors.destroy')->only(['destroy']);
        $this->middleware('permission:doctors.show')->only(['show']);
    }

    /**
     * Display a listing of doctors
     */
    public function index()
    {
        $user = Auth::user();
        $company_id = $user->company_id ?? Company::first()->id;

        return view('clinic.doctors.index', compact('company_id'));
    }

    /**
     * Get doctors data for datatables
     */
    public function getDoctors(Request $request)
    {
        $user = Auth::user();
        $company_id = $request->get('company_id', $user->company_id ?? Company::first()->id);

        $doctors = Doctor::with(['company', 'user'])
            ->where('company_id', $company_id)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('codigo_medico', 'like', "%{$search}%")
                        ->orWhere('numero_jvpm', 'like', "%{$search}%")
                        ->orWhere('nombres', 'like', "%{$search}%")
                        ->orWhere('apellidos', 'like', "%{$search}%")
                        ->orWhere('especialidad', 'like', "%{$search}%")
                        ->orWhere('telefono', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($doctors);
    }

    /**
     * Show the form for creating a new doctor
     */
    public function create()
    {
        $users = User::whereDoesntHave('doctor')->get();
        return view('clinic.doctors.create', compact('users'));
    }

    /**
     * Store a newly created doctor
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_jvpm' => 'required|string|max:50|unique:doctors',
            'user_id' => 'nullable|exists:users,id',
            'nombres' => 'required|string|max:200',
            'apellidos' => 'required|string|max:200',
            'especialidad' => 'required|string|max:150',
            'especialidades_secundarias' => 'nullable|string',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:150',
            'direccion_consultorio' => 'nullable|string',
            'horario_atencion' => 'nullable|string',
        ]);

        $user = Auth::user();
        $validated['company_id'] = $request->company_id ?? $user->company_id ?? Company::first()->id;
        
        // Generar código de médico único
        $validated['codigo_medico'] = 'MED-' . strtoupper(uniqid());
        $validated['estado'] = 'activo';

        $doctor = Doctor::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Médico creado exitosamente',
            'doctor' => $doctor
        ]);
    }

    /**
     * Display the specified doctor
     */
    public function show($id)
    {
        $doctor = Doctor::with(['appointments', 'consultations'])->findOrFail($id);
        
        return view('clinic.doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified doctor
     */
    public function edit($id)
    {
        $doctor = Doctor::findOrFail($id);
        $users = User::whereDoesntHave('doctor')->orWhere('id', $doctor->user_id)->get();
        
        return view('clinic.doctors.edit', compact('doctor', 'users'));
    }

    /**
     * Update the specified doctor
     */
    public function update(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);

        $validated = $request->validate([
            'numero_jvpm' => 'required|string|max:50|unique:doctors,numero_jvpm,' . $id,
            'user_id' => 'nullable|exists:users,id',
            'nombres' => 'required|string|max:200',
            'apellidos' => 'required|string|max:200',
            'especialidad' => 'required|string|max:150',
            'especialidades_secundarias' => 'nullable|string',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:150',
            'direccion_consultorio' => 'nullable|string',
            'horario_atencion' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo,suspendido',
        ]);

        $doctor->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Médico actualizado exitosamente',
            'doctor' => $doctor
        ]);
    }

    /**
     * Remove the specified doctor
     */
    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Médico eliminado exitosamente'
        ]);
    }

    /**
     * Get active doctors for select dropdown
     */
    public function getActiveDoctors(Request $request)
    {
        $user = Auth::user();
        $company_id = $request->get('company_id', $user->company_id ?? Company::first()->id);

        $doctors = Doctor::where('company_id', $company_id)
            ->where('estado', 'activo')
            ->orderBy('nombres')
            ->get(['id', 'nombres', 'apellidos', 'especialidad']);

        return response()->json($doctors);
    }
}

