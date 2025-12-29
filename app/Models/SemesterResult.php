<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\DecisionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemesterResult extends Model
{
    use HasFactory;

    protected $table = 'semester_results';

    protected $fillable = ['student_id', 'academic_year_id', 'semester', 'total_credits_enrolled', 'total_credits_earned', 'semester_average', 'semester_gpa', 'decision', 'calculated_by', 'calculated_at'];

    protected $casts = [
        'student_id' => 'string',
        'academic_year_id' => 'string',
        'semester' => 'integer',
        'total_credits_enrolled' => 'float',
        'total_credits_earned' => 'float',
        'semester_average' => 'float',
        'semester_gpa' => 'float',
        'decision' => DecisionType::class,
        'calculated_by' => 'string',
        'calculated_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(\App\Models\AcademicYear::class, 'academic_year_id');
    }

    public function calculatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'calculated_by');
    }
}
