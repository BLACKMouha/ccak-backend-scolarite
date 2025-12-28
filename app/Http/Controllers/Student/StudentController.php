<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Http\Requests\Student\UpdateStudentStatusRequest;
use App\Models\Student;
use App\Models\User;
use App\Services\Student\StudentNumberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class StudentController extends BaseApiController
{
    public function __construct(
        private StudentNumberService $studentNumberService
    ) {
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
    public function index(Request $request): JsonResponse
    {
        $students = QueryBuilder::for(Student::query())
            ->with('user')
            ->allowedIncludes(['user'])
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::partial('full_name'),
                AllowedFilter::partial('student_number'),
            ])
            ->allowedSorts(['id', 'student_number', 'full_name', 'status', 'created_at'])
            ->defaultSort('-created_at')
            ->paginate($request->get('per_page', 1000));

        Log::info('Students listed', ['count' => $students->total(), 'per_page' => $request->get('per_page', 15)]);

        return $this->success($students, 'Liste des étudiants récupérée avec succès.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Vérifier que l'utilisateur existe et n'a pas déjà un profil étudiant
            $user = User::findOrFail($request->user_id);

            Log::info('User found', ['user_id' => $request->user_id, 'user' => $user]);

            if ($user->student) {
                return $this->error('Un profil étudiant existe déjà pour cet utilisateur.', 409);
            }

            // Générer le numéro d'étudiant
            $studentNumber = $this->studentNumberService->generate();

            // Créer le profil étudiant
            $student = Student::create([
                'user_id' => $request->user_id,
                'student_number' => $studentNumber,
                'full_name' => $request->full_name,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'place_of_birth' => $request->place_of_birth,
                'nationality' => $request->nationality,
                'phone' => $request->phone,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'address' => $request->address,
                'photo_url' => $request->photo_url,
                'status' => $request->status ?? 'ACTIVE',
            ]);

            Log::info('Student created', ['id' => $student->id, 'student_number' => $student->student_number]);

            DB::commit();

            return $this->success(
                $student->load('user'),
                'Profil étudiant créé avec succès.',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Erreur lors de la création du profil étudiant.', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $student = Student::with(['user', 'guardians', 'documents'])->findOrFail($id);

            return $this->success(
                $student,
                'Profil étudiant récupéré avec succès.'
            );
        } catch (\Exception $e) {
            return $this->error('Étudiant non trouvé.', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, string $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $student = Student::findOrFail($id);

            $student->update($request->validated());

            DB::commit();

            return $this->success(
                $student->load('user'),
                'Profil étudiant mis à jour avec succès.'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Erreur lors de la mise à jour du profil étudiant.', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
