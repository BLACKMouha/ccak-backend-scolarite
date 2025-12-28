<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\CourseEnrollmentRepository;
use App\Http\Requests\CourseEnrollment\StoreCourseEnrollmentRequest;
use App\Http\Requests\CourseEnrollment\UpdateCourseEnrollmentRequest;
use App\Http\Resources\CourseEnrollmentResource;
use App\Http\Resources\CourseEnrollmentCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseEnrollmentController extends BaseApiController
{
    public function __construct(private readonly CourseEnrollmentRepository $repository) {
        $this->middleware('permission:academic_programs.view')->only(['index', 'show']);
        $this->middleware('permission:academic_programs.create')->only('store');
        $this->middleware('permission:academic_programs.update')->only('update');
        $this->middleware('permission:academic_programs.delete')->only('destroy');
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->integer('per_page') ?: 15);
        return $this->success($this->repository->paginate($perPage), 'Course enrollments retrieved successfully', 200);
        // return $response;
    }

    public function store(StoreCourseEnrollmentRequest $request): JsonResponse
    {
        $item = $this->repository->create($request->validated());
        return response()->json(new CourseEnrollmentResource($item), 201);
    }

    public function show(int|string $courseEnrollment): JsonResponse
    {
        return response()->json(new CourseEnrollmentResource($this->repository->find($courseEnrollment)));
    }

    public function update(UpdateCourseEnrollmentRequest $request, int|string $courseEnrollment): JsonResponse
    {
        $item = $this->repository->update($courseEnrollment, $request->validated());
        return response()->json(new CourseEnrollmentResource($item));
    }

    public function destroy(int|string $courseEnrollment): JsonResponse
    {
        $this->repository->delete($courseEnrollment);
        return response()->json(null, 204);
    }
}
