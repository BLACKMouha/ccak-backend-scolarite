<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class AcademicProgram extends Model
{
    use HasFactory;
    use UsesUuidV7;

    public const LEVELS = ['LICENCE', 'MASTER', 'DOCTORAT'];

    protected $fillable = [
        'department_id',
        'name',
        'level',
        'duration_semesters',
        'total_credits_required',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function courseUnits(): HasMany
    {
        return $this->hasMany(CourseUnit::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
