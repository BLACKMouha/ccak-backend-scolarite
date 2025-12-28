<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\UsesUuidV7;
use App\Models\Enums\GradeStatus;
use App\Models\Enums\GradeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;
    use UsesUuidV7;

    protected $table = 'grades';

    protected $fillable = ['course_enrollment_id', 'student_id', 'course_id', 'type', 'score', 'max_score', 'weight', 'entered_by', 'status', 'entered_at', 'validated_at'];

    protected $casts = [
        'course_enrollment_id' => 'string',
        'student_id' => 'string',
        'course_id' => 'string',
        'type' => GradeType::class,
        'score' => 'float',
        'max_score' => 'float',
        'weight' => 'float',
        'entered_by' => 'string',
        'status' => GradeStatus::class,
        'entered_at' => 'datetime',
        'validated_at' => 'datetime',
    ];

    public function courseEnrollment()
    {
        return $this->belongsTo(\App\Models\CourseEnrollment::class, 'course_enrollment_id');
    }

    public function student()
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(\App\Models\Course::class, 'course_id');
    }

    public function enteredBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'entered_by');
    }
}
