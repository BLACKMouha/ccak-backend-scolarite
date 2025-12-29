<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\BaseApiController;
use App\Repositories\EnrollmentRepository;
use App\Http\Requests\Enrollment\StoreEnrollmentRequest;
use App\Http\Requests\Enrollment\UpdateEnrollmentRequest;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\EnrollmentCollection;
use App\Models\AcademicProgram;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EnrollmentController extends BaseApiController
{
    public function __construct(private readonly EnrollmentRepository $repository) {}

    public function index(Request $request): JsonResponse
    {
        $query = Enrollment::with([
            'student', 'academicProgram', 'academicYear']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by academic year
        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        // Filter by student
        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $enrollments = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $enrollments,
        ]);

        //$perPage = (int) ($request->integer('per_page') ?: 15);
        //return response()->json(new EnrollmentCollection($this->repository->paginate($perPage)));
    }

    public function store(StoreEnrollmentRequest $request): JsonResponse
    {
        // Validation
        $validator = Validator::make($request->all(), Enrollment::validationRules());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if student exists and is active
        $student = Student::find($request->student_id);
        if (!$student || !$student->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Étudiant non trouvé ou inactif.',
            ], 404);
        }

        // Check if academic program exists and is active
        $program = AcademicProgram::find($request->academic_program_id);
        if (!$program || !$program->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Programme académique non trouvé ou inactif.',
            ], 404);
        }

        // Check if academic year exists and is active
        $year = AcademicYear::find($request->academic_year_id);
        if (!$year || !$year->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Année académique non trouvée ou inactive.',
            ], 404);
        }

        // Check for duplicates
        if (Enrollment::isDuplicate($request->student_id, $request->academic_program_id, $request->academic_year_id)) {
            return response()->json([
                'success' => false,
                'message' => 'L\'étudiant est déjà inscrit à ce programme pour cette année académique.',
            ], 422);
        }

        // Create enrollment with transaction
        DB::beginTransaction();
        try {
            $enrollmentData = $request->all();

            // Set initial semester if not provided
            if (!isset($enrollmentData['current_semester'])) {
                $enrollmentData['current_semester'] = 1;
            }

            // Set enrollment date to today if not provided
            if (!isset($enrollmentData['enrollment_date'])) {
                $enrollmentData['enrollment_date'] = now();
            }

            // Set default status if not provided
            if (!isset($enrollmentData['status'])) {
                $enrollmentData['status'] = Enrollment::STATUS_PENDING;
            }

            $enrollment = Enrollment::create($enrollmentData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Inscription créée avec succès.',
                'data' => [
                    'enrollment_id' => $enrollment->id,
                    'enrollment' => $enrollment->load(['student', 'academicProgram', 'academicYear']),
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'inscription.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int|string $id): JsonResponse
    {
        $enrollment = Enrollment::with([
            'student',
            'academicProgram',
            'academicYear',
            'courseEnrollments.course'
        ])->find($id);

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Inscription non trouvée.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $enrollment,
        ]);
    }

    public function update(UpdateEnrollmentRequest $request, int|string $id): JsonResponse
    {
        $enrollment = Enrollment::find($id);
        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Inscription non trouvée.',
            ], 404);
        }
        $validator = Validator::make($request->all(), Enrollment::validationRules($id));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check for duplicates (excluding current enrollment)
        if (Enrollment::isDuplicate(
            $request->student_id,
            $request->academic_program_id,
            $request->academic_year_id,
            $id
        )) {
            return response()->json([
                'success' => false,
                'message' => 'L\'étudiant est déjà inscrit à ce programme pour cette année académique.',
            ], 422);
        }


        $enrollment->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Inscription mise à jour avec succès.',
            'data' => $enrollment->fresh(['student', 'academicProgram', 'academicYear']),
        ]);
    }

    public function destroy(int|string $id): JsonResponse
    {
        $enrollment = Enrollment::find($id);

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Inscription non trouvée.',
            ], 404);
        }

        $enrollment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inscription supprimée avec succès.',
        ]);
    }

    /**
     * Get all enrollments for a student
    */
    public function getByStudent(Request $request, string $studentId): JsonResponse
    {
        $student = Student::find($studentId);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Étudiant non trouvé.',
            ], 404);
        }

        $query = $student->enrollments()
            ->with([
                'academicProgram:id,name,level',
                'academicYear:id,name,start_date,end_date',
                'courseEnrollments.course:id,code,name,credits'
            ]);

        // Filter by academic year
        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->orderBy('enrollment_date', 'desc')->get();

        // Format the response
        $formattedEnrollments = $enrollments->map(function ($enrollment) {
            return [
                'id' => $enrollment->id,
                'student' => [
                    'id' => $enrollment->student->id,
                    'student_number' => $enrollment->student->student_number,
                    'full_name' => $enrollment->student->full_name,
                ],
                'program' => [
                    'id' => $enrollment->academicProgram->id,
                    'name' => $enrollment->academicProgram->name,
                    'level' => $enrollment->academicProgram->level,
                ],
                'academic_year' => [
                    'id' => $enrollment->academicYear->id,
                    'name' => $enrollment->academicYear->name,
                ],
                'current_semester' => $enrollment->current_semester,
                'status' => $enrollment->status,
                'enrollment_date' => $enrollment->enrollment_date->format('Y-m-d'),
                'registration_fee_paid' => $enrollment->registration_fee_paid,
                'is_scholarship' => $enrollment->is_scholarship,
                'courses' => $enrollment->courseEnrollments->map(function ($ce) {
                    return [
                        'id' => $ce->id,
                        'course_id' => $ce->course->id,
                        'code' => $ce->course->code,
                        'name' => $ce->course->name,
                        'credits' => $ce->course->credits,
                        'semester' => $ce->semester,
                        'status' => $ce->status,
                        'enrollment_date' => $ce->enrollment_date->format('Y-m-d'),
                        'drop_date' => $ce->drop_date?->format('Y-m-d'),
                    ];
                }),
                'total_courses' => $enrollment->courseEnrollments->count(),
                'active_courses' => $enrollment->courseEnrollments->where('status', 'ENROLLED')->count(),
                'completed_courses' => $enrollment->courseEnrollments->where('status', 'COMPLETED')->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'student_number' => $student->student_number,
                    'full_name' => $student->full_name,
                    'email' => $student->email,
                ],
                'enrollments' => $formattedEnrollments,
                'total_enrollments' => $formattedEnrollments->count(),
            ],
        ]);
    }
}
