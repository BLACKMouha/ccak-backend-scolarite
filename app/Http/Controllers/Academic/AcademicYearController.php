<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\BaseApiController;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AcademicYearController extends BaseApiController
{

    public function index(): JsonResponse
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $academicYears,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $academicYear = AcademicYear::find($id);

        if (!$academicYear) {
            return response()->json([
                'success' => false,
                'message' => 'Année académique non trouvée.',
            ], 404);
        }

        $validator = Validator::make($request->all(), AcademicYear::validationRules($id));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $academicYear->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Année académique mise à jour avec succès.',
            'data' => $academicYear->fresh(),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $academicYear = AcademicYear::find($id);

        if (!$academicYear) {
            return response()->json([
                'success' => false,
                'message' => 'Année académique non trouvée.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $academicYear,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), AcademicYear::validationRules());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // If setting as current, ensure only one current year exists
        if ($request->is_current) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
        }

        $academicYear = AcademicYear::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Année académique créée avec succès.',
            'data' => $academicYear,
        ], 201);
    }

     public function destroy(string $id): JsonResponse
    {
        $academicYear = AcademicYear::find($id);

        if (!$academicYear) {
            return response()->json([
                'success' => false,
                'message' => 'Année académique non trouvée.',
            ], 404);
        }

        // Check if there are enrollments for this year
        if ($academicYear->enrollments()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer une année académique avec des inscriptions existantes.',
            ], 422);
        }

        $academicYear->delete();

        return response()->json([
            'success' => true,
            'message' => 'Année académique supprimée avec succès.',
        ]);
    }

    /**
     * Get the current academic year
    */
    public function current(): JsonResponse
    {
        $currentYear = AcademicYear::getCurrentYear();

        if (!$currentYear) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune année académique actuelle n\'est définie.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $currentYear,
        ]);
    }

    /**
     * Set an academic year as current
     */
    public function setCurrent(string $id): JsonResponse
    {
        $academicYear = AcademicYear::find($id);

        if (!$academicYear) {
            return response()->json([
                'success' => false,
                'message' => 'Année académique non trouvée.',
            ], 404);
        }

        // Remove current status from all other years
        AcademicYear::where('id', '!=', $id)->update(['is_current' => false]);

        // Set this year as current
        $academicYear->update(['is_current' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Année académique définie comme actuelle.',
            'data' => $academicYear->fresh(),
        ]);
    }
}
