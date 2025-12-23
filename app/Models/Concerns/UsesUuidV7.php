<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Ramsey\Uuid\Uuid;

trait UsesUuidV7
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    public function newUniqueId(): string
    {
        return Uuid::uuid7()->toString();
    }
}
