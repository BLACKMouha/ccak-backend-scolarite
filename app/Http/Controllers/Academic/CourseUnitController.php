<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\StoreCourseUnitRequest;
use App\Http\Requests\Academic\UpdateCourseUnitRequest;
use App\Models\CourseUnit;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CourseUnitController extends Controller
{
    public function index()
    {
        $units = QueryBuilder::for(CourseUnit::query())
            ->with(['academicProgram', 'courses'])
            ->allowedIncludes(['academicProgram', 'courses'])
            ->allowedFilters([
                AllowedFilter::exact('academic_program_id'),
                AllowedFilter::exact('semester_number'),
                AllowedFilter::callback('is_active', function ($query, $value) {
                    $isActive = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    if ($isActive === null) {
                        return;
                    }

                    $query->where('is_active', $isActive);
                }),
            ])
            ->allowedSorts(['semester_number', 'code', 'name', 'created_at'])
            ->defaultSort('semester_number')
            ->get();

        return response()->json($units);
    }

    public function store(StoreCourseUnitRequest $request)
    {
        $unit = CourseUnit::create($request->validated());

        return response()->json($unit->load(['academicProgram', 'courses']), Response::HTTP_CREATED);
    }

    public function show(CourseUnit $courseUnit)
    {
        return response()->json($courseUnit->load(['academicProgram', 'courses']));
    }

    public function update(UpdateCourseUnitRequest $request, CourseUnit $courseUnit)
    {
        $courseUnit->update($request->validated());

        return response()->json($courseUnit->refresh()->load(['academicProgram', 'courses']));
    }

    public function destroy(CourseUnit $courseUnit)
    {
        $courseUnit->delete();

        return response()->noContent();
    }
}
