<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $table = 'course_enrollments';

    protected $fillable = ['student_id', 'course_id'];

    protected $casts = [
        'student_id' => 'string',
        'course_id' => 'string',
    ];

    public function student()
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(\App\Models\Course::class, 'course_id');
    }
}