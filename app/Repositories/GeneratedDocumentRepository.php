<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\GeneratedDocument;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class GeneratedDocumentRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return GeneratedDocument::query()->latest('id')->paginate($perPage);
    }

    /** @return Collection<int, GeneratedDocument> */
    public function all(): Collection
    {
        return GeneratedDocument::query()->latest('id')->get();
    }

    public function find(int|string $id): GeneratedDocument
    {
        return GeneratedDocument::query()->findOrFail($id);
    }

    public function create(array $data): GeneratedDocument
    {
        return GeneratedDocument::query()->create($data);
    }

    public function update(int|string $id, array $data): GeneratedDocument
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
