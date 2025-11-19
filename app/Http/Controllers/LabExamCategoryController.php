<?php

namespace App\Http\Controllers;

use App\Models\LabExamCategory;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LabExamCategoryController extends Controller
{
    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'orden' => 'integer|min:0',
        ]);

        $user = Auth::user();
        $validated['company_id'] = $request->company_id ?? $user->company_id ?? Company::first()->id;
        
        // Generar código único
        $validated['codigo'] = 'CAT-' . strtoupper(uniqid());
        $validated['activo'] = true;

        $category = LabExamCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Categoría creada exitosamente',
            'category' => $category
        ]);
    }

    /**
     * Get all categories
     */
    public function index()
    {
        $user = Auth::user();
        $company_id = request()->get('company_id', $user->company_id ?? Company::first()->id);

        $categories = LabExamCategory::where('company_id', $company_id)
            ->orderBy('orden')
            ->get();

        return response()->json($categories);
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, $id)
    {
        $category = LabExamCategory::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'orden' => 'integer|min:0',
            'activo' => 'boolean',
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada exitosamente',
            'category' => $category
        ]);
    }

    /**
     * Remove the specified category
     */
    public function destroy($id)
    {
        $category = LabExamCategory::findOrFail($id);
        
        // Verificar si tiene exámenes asociados
        if ($category->exams()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la categoría porque tiene exámenes asociados'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada exitosamente'
        ]);
    }
}

