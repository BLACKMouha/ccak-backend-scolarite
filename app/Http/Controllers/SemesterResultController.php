<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\SemesterResultRepository;
use App\Http\Requests\SemesterResult\StoreSemesterResultRequest;
use App\Http\Requests\SemesterResult\UpdateSemesterResultRequest;
use App\Http\Resources\SemesterResultResource;
use App\Http\Resources\SemesterResultCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SemesterResultController extends Controller
{
    public function __construct(private readonly SemesterResultRepository $repository) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->integer('per_page') ?: 15);
        return response()->json(new SemesterResultCollection($this->repository->paginate($perPage)));
    }

    public function store(StoreSemesterResultRequest $request): JsonResponse
    {
        $item = $this->repository->create($request->validated());
        return response()->json(new SemesterResultResource($item), 201);
    }

    public function show(int|string $semesterResult): JsonResponse
    {
        return response()->json(new SemesterResultResource($this->repository->find($semesterResult)));
    }

    public function update(UpdateSemesterResultRequest $request, int|string $semesterResult): JsonResponse
    {
        $item = $this->repository->update($semesterResult, $request->validated());
        return response()->json(new SemesterResultResource($item));
    }

    public function destroy(int|string $semesterResult): JsonResponse
    {
        $this->repository->delete($semesterResult);
        return response()->json(null, 204);
    }
}