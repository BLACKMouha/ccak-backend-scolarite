<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Grade;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class GradeRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Grade::query()->latest('id')->paginate($perPage);
    }

    /** @return Collection<int, Grade> */
    public function all(): Collection
    {
        return Grade::query()->latest('id')->get();
    }

    public function find(int|string $id): Grade
    {
        return Grade::query()->findOrFail($id);
    }

    public function create(array $data): Grade
    {
        return Grade::query()->create($data);
    }

    public function update(int|string $id, array $data): Grade
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
