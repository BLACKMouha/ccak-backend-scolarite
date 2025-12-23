<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\StoreAcademicProgramRequest;
use App\Http\Requests\Academic\UpdateAcademicProgramRequest;
use App\Models\AcademicProgram;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AcademicProgramController extends Controller
{
    public function index()
    {
        $programs = QueryBuilder::for(AcademicProgram::query())
            ->with(['department', 'courseUnits'])
            ->allowedIncludes(['department', 'courseUnits'])
            ->allowedFilters([
                AllowedFilter::exact('department_id'),
                AllowedFilter::exact('level'),
                AllowedFilter::callback('is_active', function ($query, $value) {
                    $isActive = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    if ($isActive === null) {
                        return;
                    }

                    $query->where('is_active', $isActive);
                }),
            ])
            ->allowedSorts(['name', 'level', 'created_at'])
            ->defaultSort('name')
            ->get();

        return response()->json($programs);
    }

    public function store(StoreAcademicProgramRequest $request)
    {
        $program = AcademicProgram::create($request->validated());

        return response()->json($program->load(['department', 'courseUnits']), Response::HTTP_CREATED);
    }

    public function show(AcademicProgram $academicProgram)
    {
        return response()->json($academicProgram->load(['department', 'courseUnits']));
    }

    public function update(UpdateAcademicProgramRequest $request, AcademicProgram $academicProgram)
    {
        $academicProgram->update($request->validated());

        return response()->json($academicProgram->refresh()->load(['department', 'courseUnits']));
    }

    public function destroy(AcademicProgram $academicProgram)
    {
        $academicProgram->delete();

        return response()->noContent();
    }
}
