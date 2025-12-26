<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Academic\StoreCourseRequest;
use App\Http\Requests\Academic\UpdateCourseRequest;
use App\Models\Course;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CourseController extends BaseApiController
{
    public function __construct()
    {
        $this->middleware('permission:courses.view')->only(['index', 'show']);
        $this->middleware('permission:courses.create')->only('store');
        $this->middleware('permission:courses.update')->only('update');
        $this->middleware('permission:courses.delete')->only('destroy');
    }

    public function index()
    {
        $courses = QueryBuilder::for(Course::query())
            ->with(['courseUnit.academicProgram'])
            ->allowedIncludes(['courseUnit', 'courseUnit.academicProgram'])
            ->allowedFilters([
                AllowedFilter::exact('course_unit_id'),
                AllowedFilter::callback('is_active', function ($query, $value) {
                    $isActive = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    if ($isActive === null) {
                        return;
                    }

                    $query->where('is_active', $isActive);
                }),
                AllowedFilter::callback('level', function ($query, $value) {
                    $level = trim((string) $value);
                    if ($level === '') {
                        return;
                    }

                    $query->whereHas('courseUnit.academicProgram', fn($sub) => $sub->where('level', $level));
                }),
                AllowedFilter::callback('search', function ($query, $value) {
                    $search = trim((string) $value);
                    if ($search === '') {
                        return;
                    }

                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
                }),
            ])
            ->allowedSorts(['name', 'code', 'credits', 'created_at'])
            ->defaultSort('name')
            ->get();

        return $this->success($courses);
    }

    public function store(StoreCourseRequest $request)
    {
        $course = Course::create($request->validated());

        return $this->success($course->load(['courseUnit.academicProgram']), 'Course created', Response::HTTP_CREATED);
    }

    public function show(Course $course)
    {
        return $this->success($course->load(['courseUnit.academicProgram']));
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $course->update($request->validated());

        return $this->success($course->refresh()->load(['courseUnit.academicProgram']), 'Course updated');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return $this->success(null, 'Course deleted');
    }
}
