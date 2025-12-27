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

class AcademicYearController extends Controller
{
    public function __construct(private readonly AcademicYearRepository $repository) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->integer('per_page') ?: 15);
        return response()->json(new AcademicYearCollection($this->repository->paginate($perPage)));
    }

    public function store(StoreAcademicYearRequest $request): JsonResponse
    {
        $item = $this->repository->create($request->validated());
        return response()->json(new AcademicYearResource($item), 201);
    }

    public function show(int|string $academicYear): JsonResponse
    {
        return response()->json(new AcademicYearResource($this->repository->find($academicYear)));
    }

    public function update(UpdateAcademicYearRequest $request, int|string $academicYear): JsonResponse
    {
        $item = $this->repository->update($academicYear, $request->validated());
        return response()->json(new AcademicYearResource($item));
    }

    public function destroy(int|string $academicYear): JsonResponse
    {
        $this->repository->delete($academicYear);
        return response()->json(null, 204);
    }
}