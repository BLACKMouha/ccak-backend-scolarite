<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Academic\StoreAcademicYearRequest;
use App\Http\Requests\Academic\UpdateAcademicYearRequest;
use App\Models\AcademicYear;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AcademicYearController extends BaseApiController
{
    public function __construct()
    {
        // $this->middleware('permission:academic_years.view')->only(['index', 'show']);
        // $this->middleware('permission:academic_years.create')->only('store');
        // $this->middleware('permission:academic_years.update')->only('update');
        // $this->middleware('permission:academic_years.delete')->only('destroy');
    }

    public function index()
    {
        $years = QueryBuilder::for(AcademicYear::query())
            ->allowedSorts(['name', 'created_at'])
            ->defaultSort('-created_at')
            ->get();

        return $this->success($years);
    }

    public function store(StoreAcademicYearRequest $request)
    {
        $year = AcademicYear::create($request->validated());

        return $this->success($year, 'Academic year created', Response::HTTP_CREATED);
    }

    public function show(AcademicYear $academicYear)
    {
        return $this->success($academicYear);
    }

    public function update(UpdateAcademicYearRequest $request, AcademicYear $academicYear)
    {
        $academicYear->update($request->validated());

        return $this->success($academicYear->refresh(), 'Academic year updated');
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();

        return $this->success(null, 'Academic year deleted');
    }
}
