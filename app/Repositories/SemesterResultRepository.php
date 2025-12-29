<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\SemesterResult;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SemesterResultRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return SemesterResult::query()->latest('id')->paginate($perPage);
    }

    /** @return Collection<int, SemesterResult> */
    public function all(): Collection
    {
        return SemesterResult::query()->latest('id')->get();
    }

    public function find(int|string $id): SemesterResult
    {
        return SemesterResult::query()->findOrFail($id);
    }

    public function create(array $data): SemesterResult
    {
        return SemesterResult::query()->create($data);
    }

    public function update(int|string $id, array $data): SemesterResult
    {
        $item = $this->find($id);
        $item->update($data);
        return $item;
    }

    public function delete(int|string $id): void
    {
        $item = $this->find($id);
        $item->delete();
    }
}
