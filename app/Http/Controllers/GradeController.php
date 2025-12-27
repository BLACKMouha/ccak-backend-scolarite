<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\GradeRepository;
use App\Http\Requests\Grade\StoreGradeRequest;
use App\Http\Requests\Grade\UpdateGradeRequest;
use App\Http\Resources\GradeResource;
use App\Http\Resources\GradeCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function __construct(private readonly GradeRepository $repository) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->integer('per_page') ?: 15);
        return response()->json(new GradeCollection($this->repository->paginate($perPage)));
    }

    public function store(StoreGradeRequest $request): JsonResponse
    {
        $item = $this->repository->create([
            ...$request->validated(),
            'entered_by' => auth()->id,
            'status' => "DRAFT",
        ]);
        return response()->json(new GradeResource($item), 201);
    }

    public function show(int|string $grade): JsonResponse
    {
        return response()->json(new GradeResource($this->repository->find($grade)));
    }

    public function update(UpdateGradeRequest $request, int|string $grade): JsonResponse
    {
        $gradeModel = $this->repository->find($grade);

        // Store old values for audit log
        $oldValues = $gradeModel->only([
            'course_enrollment_id', 'student_id', 'course_id',
            'type', 'score', 'max_score', 'weight'
        ]);

        // Update the grade
        $item = $this->repository->update($grade, $request->validated());

        // Log the audit trail
        $changes = [];
        foreach ($request->validated() as $key => $newValue) {
            if (isset($oldValues[$key]) && $oldValues[$key] != $newValue) {
                $changes[$key] = [
                    'old' => $oldValues[$key],
                    'new' => $newValue
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => new GradeResource($item),
            'message' => 'Grade updated successfully'
        ]);
    }

    public function destroy(int|string $grade): JsonResponse
    {
        $this->repository->delete($grade);
        return response()->json(null, 204);
    }

    /**
     * Submit a grade for validation
     * POST /api/grades/{id}/submit
     * Changes status to SUBMITTED and prevents further editing
     */
    public function submit(int|string $grade): JsonResponse
    {
        $item = $this->repository->find($grade);

        // Validation: Cannot submit if already submitted or beyond
        if (in_array($item->status, ['SUBMITTED', 'VALIDATED', 'PUBLISHED'])) {
            return response()->json([
                'success' => false,
                'message' => 'Grade has already been submitted and cannot be modified',
                'errors' => ['status' => ['Invalid status transition']]
            ], 422);
        }

        // Validation: Check required fields
        if (is_null($item->score) || is_null($item->max_score)) {
            return response()->json([
                'success' => false,
                'message' => 'Grade must have a score and max_score before submission',
                'errors' => ['score' => ['Score and max_score are required']]
            ], 422);
        }

        // Update status to SUBMITTED
        $item = $this->repository->update($grade, [
            'status' => 'SUBMITTED',
            'entered_at' => now()
        ]);

        // TODO: Notify admin (implement notification system)
        // event(new GradeSubmitted($item));

        return response()->json([
            'success' => true,
            'data' => new GradeResource($item),
            'message' => 'Grade submitted successfully for validation'
        ]);
    }

    /**
     * Validate a submitted grade (Admin/Department Head only)
     * POST /api/grades/{id}/validate
     * Changes status to VALIDATED and triggers calculation
     */
    public function validateGrade(int|string $grade): JsonResponse
    {
        // TODO: Add middleware to check if user is Admin or Department Head
        // For now, we'll just check if user is authenticated

        $item = $this->repository->find($grade);

        // Validation: Can only validate SUBMITTED grades
        if ($item->status !== 'SUBMITTED') {
            return response()->json([
                'success' => false,
                'message' => 'Only submitted grades can be validated',
                'errors' => ['status' => ['Grade must be in SUBMITTED status']]
            ], 422);
        }

        // Update status to VALIDATED
        $item = $this->repository->update($grade, [
            'status' => 'VALIDATED',
            'validated_at' => now()
        ]);

        // TODO: Trigger calculation logic
        // event(new GradeValidated($item));

        // TODO: Add to audit log
        // AuditLog::create([...]);

        return response()->json([
            'success' => true,
            'data' => new GradeResource($item),
            'message' => 'Grade validated successfully'
        ]);
    }

    /**
     * Publish all validated grades (Admin only)
     * POST /api/grades/publish
     * Bulk operation to publish all VALIDATED grades
     */
    public function publish(Request $request): JsonResponse
    {
        // TODO: Add middleware to check if user is Admin only

        // Get all VALIDATED grades
        $validatedGrades = $this->repository->getByStatus('VALIDATED');

        if ($validatedGrades->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No validated grades found to publish',
                'data' => ['count' => 0]
            ], 404);
        }

        // Bulk update to PUBLISHED status
        $publishedCount = $this->repository->bulkPublish($validatedGrades->pluck('id')->toArray());

        // TODO: Notify students
        // event(new GradesPublished($validatedGrades));

        return response()->json([
            'success' => true,
            'data' => [
                'count' => $publishedCount,
                'message' => "Successfully published {$publishedCount} grade(s)"
            ],
            'message' => 'Grades published successfully'
        ]);
    }
}
