<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseEnrollment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'enrollment_id',
        'course_id',
        'academic_year_id',
        'semester',
        'status',
        'enrollment_date',
        'drop_date',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'drop_date' => 'date',
        'semester' => 'integer',
    ];
}
