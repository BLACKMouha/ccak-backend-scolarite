<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\StoreDepartmentRequest;
use App\Http\Requests\Academic\UpdateDepartmentRequest;
use App\Models\Department;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = QueryBuilder::for(Department::query())
            ->with(['faculty', 'head', 'programs'])
            ->allowedIncludes(['faculty', 'head', 'programs'])
            ->allowedFilters([
                AllowedFilter::exact('faculty_id'),
                AllowedFilter::callback('is_active', function ($query, $value) {
                    $isActive = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    if ($isActive === null) {
                        return;
                    }

                    $query->where('is_active', $isActive);
                }),
            ])
            ->allowedSorts(['name', 'code', 'created_at'])
            ->defaultSort('name')
            ->get();

        return response()->json($departments);
    }

    public function store(StoreDepartmentRequest $request)
    {
        $department = Department::create($request->validated());

        return response()->json($department->load(['faculty', 'head', 'programs']), Response::HTTP_CREATED);
    }

    public function show(Department $department)
    {
        return response()->json($department->load(['faculty', 'head', 'programs']));
    }

    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $department->update($request->validated());

        return response()->json($department->refresh()->load(['faculty', 'head', 'programs']));
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return response()->noContent();
    }
}
