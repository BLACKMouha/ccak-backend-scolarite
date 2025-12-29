<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\StudentRepository;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Enums\GradeStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends BaseApiController
{
    public function __construct(private readonly StudentRepository $repository) {
        // $this->middleware('permission:students.view')->only(['index', 'show', 'grades']);
        // $this->middleware('permission:students.create')->only('store');
        // $this->middleware('permission:students.update')->only('update');
        // $this->middleware('permission:students.delete')->only('destroy');
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->integer('per_page') ?: 15);
        return $this->success($this->repository->paginate($perPage), 'Students retrieved successfully');
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {
        $item = $this->repository->create($request->validated());
        return $this->success(new StudentResource($item), 'Student created successfully', 201);
    }

    public function show(int|string $student): JsonResponse
    {
        return $this->success(new StudentResource($this->repository->find($student)), 'Student retrieved successfully');
    }

    public function update(UpdateStudentRequest $request, int|string $student): JsonResponse
    {
        $item = $this->repository->update($student, $request->validated());
        return $this->success(new StudentResource($item), 'Student updated successfully');
    }

    public function destroy(int|string $student): JsonResponse
    {
        $this->repository->delete($student);
        return $this->success(null, 'Student deleted successfully', 204);
    }

    public function grades(Request $request, int|string $student): JsonResponse
    {
        $studentModel = $this->repository->find($student);

        $query = $studentModel->grades()
            ->with(['course', 'courseEnrollment'])
            ->orderBy('created_at', 'desc');

        if ($request->has('semester')) {
            $query->whereHas('courseEnrollment', function ($q) use ($request) {
                $q->where('semester', $request->get('semester'));
            });
        }

        if ($request->has('academic_year')) {
            $query->whereHas('courseEnrollment', function ($q) use ($request) {
                $q->where('academic_year', $request->get('academic_year'));
            });
        }

        $user = $request->user();
        if ($user && $this->isStudent($user)) {
            $query->where('status', GradeStatus::PUBLISHED);
        }

        $grades = $query->get();

        $groupedGrades = $grades->groupBy('course_id')->map(function ($courseGrades) {
            $course = $courseGrades->first()->course;
            $gradesByType = $courseGrades->groupBy('type');

            $averageScore = $courseGrades->avg('score');
            $maxPossibleScore = $courseGrades->avg('max_score');
            $weightedAverage = $courseGrades->sum(function ($grade) {
                return ($grade->score / $grade->max_score) * $grade->weight;
            }) / $courseGrades->sum('weight');

            return [
                'course' => [
                    'id' => $course->id,
                    'code' => $course->code,
                    'name' => $course->name,
                    'credits' => $course->credits,
                ],
                'grades' => $gradesByType->map(function ($grades, $type) {
                    return [
                        'type' => $type,
                        'grades' => $grades->map(function ($grade) {
                            return [
                                'id' => $grade->id,
                                'score' => $grade->score,
                                'max_score' => $grade->max_score,
                                'weight' => $grade->weight,
                                'status' => $grade->status,
                                'entered_at' => $grade->entered_at,
                                'validated_at' => $grade->validated_at,
                            ];
                        })->values(),
                        'average' => $grades->avg('score'),
                        'max_average' => $grades->avg('max_score'),
                    ];
                }),
                'averages' => [
                    'simple_average' => round($averageScore, 2),
                    'max_possible' => round($maxPossibleScore, 2),
                    'weighted_average' => round($weightedAverage * 100, 2),
                    'percentage' => round(($averageScore / $maxPossibleScore) * 100, 2),
                ],
                'total_grades' => $courseGrades->count(),
            ];
        })->values();

        return $this->success([
            'student' => [
                'id' => $studentModel->id,
                'student_number' => $studentModel->student_number,
                'full_name' => $studentModel->full_name,
            ],
            'courses' => $groupedGrades,
            'summary' => [
                'total_courses' => $groupedGrades->count(),
                'total_grades' => $grades->count(),
                'overall_average' => round($grades->avg('score'), 2),
            ],
        ], 'Student grades retrieved successfully');
    }

    private function isStudent($user): bool
    {
        return $user->hasRole('student');
    }
}
