<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\StudentRepository;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Http\Resources\StudentCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct(private readonly StudentRepository $repository) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->integer('per_page') ?: 15);
        return response()->json(new StudentCollection($this->repository->paginate($perPage)));
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {
        $item = $this->repository->create($request->validated());
        return response()->json(new StudentResource($item), 201);
    }

    public function show(int|string $student): JsonResponse
    {
        return response()->json(new StudentResource($this->repository->find($student)));
    }

    public function update(UpdateStudentRequest $request, int|string $student): JsonResponse
    {
        $item = $this->repository->update($student, $request->validated());
        return response()->json(new StudentResource($item));
    }

    public function destroy(int|string $student): JsonResponse
    {
        $this->repository->delete($student);
        return response()->json(null, 204);
    }
}