<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DeliberationSession extends Model
{
    /** @use HasFactory<\Database\Factories\DeliberationSessionFactory> */
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'academic_program_id',
        'academic_year_id',
        'semester',
        'session_name',
        'session_date',
        'status',
        'presided_by',
        'jury_members',
    ];

    protected $casts = [
        'session_date' => 'date',
        'jury_members' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Constants for status
    const STATUS_SCHEDULED = 'SCHEDULED';
    const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    const STATUS_COMPLETED = 'COMPLETED';
    const STATUS_CLOSED = 'CLOSED';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_SCHEDULED,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
            self::STATUS_CLOSED,
        ];
    }

    // Relationships
    public function academicProgram()
    {
        return $this->belongsTo(AcademicProgram::class, 'academic_program_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function president()
    {
        return $this->belongsTo(User::class, 'presided_by');
    }

    public function results()
    {
        return $this->hasMany(DeliberationResult::class, 'deliberation_session_id');
    }
}
