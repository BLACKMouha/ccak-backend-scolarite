<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\GradeRepository;
use App\Http\Requests\Grade\StoreGradeRequest;
use App\Http\Requests\Grade\UpdateGradeRequest;
use App\Http\Resources\GradeResource;
use App\Http\Resources\GradeCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function __construct(private readonly GradeRepository $repository) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->integer('per_page') ?: 15);
        return response()->json(new GradeCollection($this->repository->paginate($perPage)));
    }

    public function store(StoreGradeRequest $request): JsonResponse
    {
        $item = $this->repository->create($request->validated());
        return response()->json(new GradeResource($item), 201);
    }

    public function show(int|string $grade): JsonResponse
    {
        return response()->json(new GradeResource($this->repository->find($grade)));
    }

    public function update(UpdateGradeRequest $request, int|string $grade): JsonResponse
    {
        $item = $this->repository->update($grade, $request->validated());
        return response()->json(new GradeResource($item));
    }

    public function destroy(int|string $grade): JsonResponse
    {
        $this->repository->delete($grade);
        return response()->json(null, 204);
    }
}