<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Student\StoreGuardianRequest;
use App\Http\Requests\Student\UpdateGuardianRequest;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GuardianController extends BaseApiController
{
    public function __construct()
    {
        // Désactiver les middlewares de permission pour les routes de test
        if (request()->is('api/test/*')) {
            return;
        }

        $this->middleware('permission:students.view')->only(['index', 'show']);
        $this->middleware('permission:students.create')->only('store');
        $this->middleware('permission:students.update')->only('update');
        $this->middleware('permission:students.delete')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Student $student): JsonResponse
    {
        $guardians = QueryBuilder::for(Guardian::where('student_id', $student->id))
            ->allowedFilters([
                AllowedFilter::exact('relationship'),
                AllowedFilter::partial('full_name'),
            ])
            ->allowedSorts(['id', 'full_name', 'relationship', 'created_at'])
            ->defaultSort('-created_at')
            ->paginate($request->get('per_page', 15));

        return $this->success($guardians, 'Liste des tuteurs récupérée avec succès.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGuardianRequest $request, Student $student): JsonResponse
    {
        try {
            $guardian = $student->guardians()->create($request->validated());

            return $this->success(
                $guardian,
                'Tuteur créé avec succès.',
                201
            );
        } catch (\Exception $e) {
            return $this->error('Erreur lors de la création du tuteur.', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student, Guardian $guardian): JsonResponse
    {
        // Vérifier que le tuteur appartient à l'étudiant
        if ($guardian->student_id !== $student->id) {
            return $this->error('Tuteur non trouvé pour cet étudiant.', 404);
        }

        return $this->success(
            $guardian,
            'Tuteur récupéré avec succès.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGuardianRequest $request, Student $student, Guardian $guardian): JsonResponse
    {
        // Vérifier que le tuteur appartient à l'étudiant
        if ($guardian->student_id !== $student->id) {
            return $this->error('Tuteur non trouvé pour cet étudiant.', 404);
        }

        try {
            $guardian->update($request->validated());

            return $this->success(
                $guardian,
                'Tuteur mis à jour avec succès.'
            );
        } catch (\Exception $e) {
            return $this->error('Erreur lors de la mise à jour du tuteur.', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student, Guardian $guardian): JsonResponse
    {
        // Vérifier que le tuteur appartient à l'étudiant
        if ($guardian->student_id !== $student->id) {
            return $this->error('Tuteur non trouvé pour cet étudiant.', 404);
        }

        try {
            $guardian->delete();

            return $this->success(
                null,
                'Tuteur supprimé avec succès.'
            );
        } catch (\Exception $e) {
            return $this->error('Erreur lors de la suppression du tuteur.', 500);
        }
    }
}
