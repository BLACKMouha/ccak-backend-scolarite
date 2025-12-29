<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\AcademicYearRepository;
use App\Http\Requests\AcademicYear\StoreAcademicYearRequest;
use App\Http\Requests\AcademicYear\UpdateAcademicYearRequest;
use App\Http\Resources\AcademicYearResource;
use App\Http\Resources\AcademicYearCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcademicYearController extends BaseApiController
{
    public function __construct(private readonly AcademicYearRepository $repository) {
        // $this->middleware('permission:academic_programs.view')->only(['index', 'show']);
        // $this->middleware('permission:academic_programs.create')->only('store');
        // $this->middleware('permission:academic_programs.update')->only('update');
        // $this->middleware('permission:academic_programs.delete')->only('destroy');
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->integer('per_page') ?: 15);
        return $this->success($this->repository->paginate($perPage), 'Academic years retrieved successfully');
    }

    public function store(StoreAcademicYearRequest $request): JsonResponse
    {
        $item = $this->repository->create($request->validated());
        return $this->success(new AcademicYearResource($item), 'Academic year created successfully', 201);
    }

    public function show(int|string $academicYear): JsonResponse
    {
        return $this->success(new AcademicYearResource($this->repository->find($academicYear)), 'Academic year retrieved successfully');
    }

    public function update(UpdateAcademicYearRequest $request, int|string $academicYear): JsonResponse
    {
        $item = $this->repository->update($academicYear, $request->validated());
        return $this->success(new AcademicYearResource($item), 'Academic year updated successfully');
    }

    public function destroy(int|string $academicYear): JsonResponse
    {
        $this->repository->delete($academicYear);
        return $this->success(null, 'Academic year deleted successfully', 204);
    }
}
