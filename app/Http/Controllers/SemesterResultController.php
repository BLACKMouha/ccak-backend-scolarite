<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\SemesterResultRepository;
use App\Http\Requests\SemesterResult\StoreSemesterResultRequest;
use App\Http\Requests\SemesterResult\UpdateSemesterResultRequest;
use App\Http\Resources\SemesterResultResource;
use App\Http\Resources\SemesterResultCollection;
use App\Services\SemesterResultCalculationService;
use App\Jobs\CalculateSemesterResultsJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SemesterResultController extends Controller
{
    public function __construct(
        private readonly SemesterResultRepository $repository,
        private readonly SemesterResultCalculationService $calculationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->integer('per_page') ?: 15);
        return response()->json(new SemesterResultCollection($this->repository->paginate($perPage)));
    }

    public function store(StoreSemesterResultRequest $request): JsonResponse
    {
        $item = $this->repository->create($request->validated());
        return response()->json(new SemesterResultResource($item), 201);
    }

    public function show(int|string $semesterResult): JsonResponse
    {
        return response()->json(new SemesterResultResource($this->repository->find($semesterResult)));
    }

    public function update(UpdateSemesterResultRequest $request, int|string $semesterResult): JsonResponse
    {
        $item = $this->repository->update($semesterResult, $request->validated());
        return response()->json(new SemesterResultResource($item));
    }

    public function destroy(int|string $semesterResult): JsonResponse
    {
        $this->repository->delete($semesterResult);
        return response()->json(null, 204);
    }

    /**
     * Calculate semester results for all students
     * POST /api/semester-results/calculate
     * Admin only endpoint that queues calculation job
     */
    public function calculate(Request $request): JsonResponse
    {
        // Validate admin authorization
        $user = $request->user();
        if (!$user || !$user->hasRole('ADMIN')) {
            return response()->json([
                'success' => false,
                'message' => 'Only administrators are authorized to calculate semester results.',
                'errors' => ['authorization' => ['Admin role required']]
            ], 403);
        }

        // Validate request data
        $validated = $request->validate([
            'academic_year_id' => 'required|string|exists:academic_years,id',
            'semester' => 'required|integer|min:1|max:2',
            'async' => 'boolean'
        ]);

        $academicYearId = $validated['academic_year_id'];
        $semester = $validated['semester'];
        $async = $validated['async'] ?? true;

        // Generate unique job ID
        $jobId = Str::uuid()->toString();

        if ($async) {
            // Queue the calculation job
            CalculateSemesterResultsJob::dispatch(
                $academicYearId,
                $semester,
                $user->id,
                $jobId
            )->onQueue('semester-calculations');

            return response()->json([
                'success' => true,
                'message' => 'Semester results calculation has been queued and will be processed asynchronously.',
                'data' => [
                    'job_id' => $jobId,
                    'academic_year_id' => $academicYearId,
                    'semester' => $semester,
                    'status' => 'queued'
                ]
            ], 202);
        } else {
            // Process synchronously (for small datasets or testing)
            try {
                $result = $this->calculationService->calculateSemesterResults(
                    $academicYearId,
                    $semester,
                    $user
                );

                return response()->json([
                    'success' => $result['success'],
                    'message' => $result['message'],
                    'data' => $result['data']
                ], $result['success'] ? 200 : 422);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to calculate semester results: ' . $e->getMessage(),
                    'errors' => ['calculation' => [$e->getMessage()]]
                ], 500);
            }
        }
    }

    /**
     * Get semester statistics
     * GET /api/semester-results/statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|string|exists:academic_years,id',
            'semester' => 'required|integer|min:1|max:2',
        ]);

        try {
            $statistics = $this->calculationService->getSemesterStatistics(
                $validated['academic_year_id'],
                $validated['semester']
            );

            return response()->json([
                'success' => true,
                'data' => $statistics,
                'message' => 'Semester statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve semester statistics: ' . $e->getMessage(),
                'errors' => ['statistics' => [$e->getMessage()]]
            ], 500);
        }
    }

    /**
     * Recalculate results for a specific student
     * POST /api/semester-results/recalculate/{student}
     */
    public function recalculateStudent(Request $request, string $studentId): JsonResponse
    {
        // Validate admin authorization
        $user = $request->user();
        if (!$user || !$user->hasRole('ADMIN')) {
            return response()->json([
                'success' => false,
                'message' => 'Only administrators are authorized to recalculate student results.',
                'errors' => ['authorization' => ['Admin role required']]
            ], 403);
        }

        $validated = $request->validate([
            'academic_year_id' => 'required|string|exists:academic_years,id',
            'semester' => 'required|integer|min:1|max:2',
        ]);

        try {
            $student = \App\Models\Student::findOrFail($studentId);

            $result = $this->calculationService->calculateStudentSemesterResult(
                $student,
                $validated['academic_year_id'],
                $validated['semester'],
                $user
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'data' => new SemesterResultResource($result),
                    'message' => 'Student semester result recalculated successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not recalculate semester result for this student',
                    'errors' => ['student' => ['No courses found or calculation failed']]
                ], 422);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to recalculate student result: ' . $e->getMessage(),
                'errors' => ['calculation' => [$e->getMessage()]]
            ], 500);
        }
    }
}