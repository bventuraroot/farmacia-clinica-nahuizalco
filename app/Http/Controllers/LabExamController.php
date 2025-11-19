<?php

namespace App\Http\Controllers;

use App\Models\LabExam;
use App\Models\LabExamCategory;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LabExamController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:lab-exams.index')->only(['index']);
        $this->middleware('permission:lab-exams.create')->only(['create', 'store']);
        $this->middleware('permission:lab-exams.edit')->only(['edit', 'update']);
        $this->middleware('permission:lab-exams.destroy')->only(['destroy']);
    }

    /**
     * Display a listing of lab exams
     */
    public function index()
    {
        $user = Auth::user();
        $company_id = $user->company_id ?? Company::first()->id;

        $categories = LabExamCategory::where('company_id', $company_id)
            ->orderBy('orden')
            ->get();

        return view('laboratory.exams.index', compact('company_id', 'categories'));
    }

    /**
     * Get exams data for datatables
     */
    public function getExams(Request $request)
    {
        $user = Auth::user();
        $company_id = $request->get('company_id', $user->company_id ?? Company::first()->id);

        $exams = LabExam::with('category')
            ->where('company_id', $company_id)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('codigo_examen', 'like', "%{$search}%")
                        ->orWhere('nombre', 'like', "%{$search}%")
                        ->orWhere('tipo_muestra', 'like', "%{$search}%");
                });
            })
            ->when($request->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->orderBy('nombre')
            ->paginate(20);

        return response()->json($exams);
    }

    /**
     * Show the form for creating a new exam
     */
    public function create()
    {
        $categories = LabExamCategory::where('activo', true)->orderBy('nombre')->get();
        
        return view('laboratory.exams.create', compact('categories'));
    }

    /**
     * Store a newly created exam
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:lab_exam_categories,id',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'preparacion_requerida' => 'nullable|string',
            'tipo_muestra' => 'required|string|max:100',
            'tiempo_procesamiento_horas' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
            'valores_referencia' => 'nullable|string',
            'requiere_ayuno' => 'boolean',
            'prioridad' => 'required|in:normal,urgente,stat',
        ]);

        $user = Auth::user();
        $validated['company_id'] = $request->company_id ?? $user->company_id ?? Company::first()->id;
        
        // Generar código de examen único
        $validated['codigo_examen'] = 'EX-' . strtoupper(uniqid());
        $validated['activo'] = true;

        $exam = LabExam::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Examen creado exitosamente',
                'exam' => $exam->load('category')
            ]);
        }

        return redirect()->route('lab-exams.index')->with('success', 'Examen creado exitosamente');
    }

    /**
     * Display the specified exam
     */
    public function show($id)
    {
        $exam = LabExam::with('category')->findOrFail($id);
        
        return response()->json($exam);
    }

    /**
     * Update the specified exam
     */
    public function update(Request $request, $id)
    {
        $exam = LabExam::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:lab_exam_categories,id',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'preparacion_requerida' => 'nullable|string',
            'tipo_muestra' => 'required|string|max:100',
            'tiempo_procesamiento_horas' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
            'valores_referencia' => 'nullable|string',
            'requiere_ayuno' => 'boolean',
            'prioridad' => 'required|in:normal,urgente,stat',
            'activo' => 'boolean',
        ]);

        $exam->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Examen actualizado exitosamente',
                'exam' => $exam->load('category')
            ]);
        }

        return redirect()->route('lab-exams.index')->with('success', 'Examen actualizado exitosamente');
    }

    /**
     * Remove the specified exam
     */
    public function destroy($id)
    {
        $exam = LabExam::findOrFail($id);
        $exam->delete();

        return response()->json([
            'success' => true,
            'message' => 'Examen eliminado exitosamente'
        ]);
    }

    /**
     * Get active exams for select dropdown
     */
    public function getActiveExams(Request $request)
    {
        $user = Auth::user();
        $company_id = $request->get('company_id', $user->company_id ?? Company::first()->id);
        $category_id = $request->get('category_id');

        $query = LabExam::with('category')
            ->where('company_id', $company_id)
            ->where('activo', true);

        if ($category_id) {
            $query->where('category_id', $category_id);
        }

        $exams = $query->orderBy('nombre')->get();

        return response()->json($exams);
    }

    /**
     * Toggle exam status
     */
    public function toggleStatus($id)
    {
        $exam = LabExam::findOrFail($id);
        $exam->activo = !$exam->activo;
        $exam->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado',
            'activo' => $exam->activo
        ]);
    }
}

