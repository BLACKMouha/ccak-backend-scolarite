<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidV7;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    use HasFactory;
    use UsesUuidV7;

    protected $fillable = [
        'course_unit_id',
        'code',
        'name',
        'description',
        'credits',
        'hours_lecture',
        'hours_td',
        'hours_tp',
        'coefficient',
        'prerequisites',
        'is_active',
    ];

    protected $casts = [
        'prerequisites' => 'array',
        'is_active' => 'boolean',
    ];

    public function courseUnit(): BelongsTo
    {
        return $this->belongsTo(CourseUnit::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getPrerequisitesAttribute($value): array
    {
        return is_array($value) ? $value : (json_decode($value, true) ?: []);
    }
}
