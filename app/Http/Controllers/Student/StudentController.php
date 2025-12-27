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

class StudentController extends BaseApiController
{
    public function __construct(
        private StudentNumberService $studentNumberService
    ) {
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
        $query = Student::with('user');

        // Filtres
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('name') && $request->name) {
            $query->where('full_name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('student_number') && $request->student_number) {
            $query->where('student_number', 'like', '%' . $request->student_number . '%');
        }

        // Tri
        $allowedSortFields = ['id', 'student_number', 'full_name', 'status', 'created_at'];
        $sortBy = $request->get('sort_by', 'created_at');
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }
        $sortOrder = $request->get('sort_order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        if ($perPage < 1 || $perPage > 100) {
            $perPage = 15;
        }

        $students = $query->paginate($perPage);

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
