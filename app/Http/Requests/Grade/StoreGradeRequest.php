<?php
declare(strict_types=1);

namespace App\Http\Requests\Grade;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
        'course_enrollment_id' => 'required|string|exists:course_enrollments,id',
        'student_id' => 'required|string|exists:students,id',
        'course_id' => 'required|string|exists:courses,id',
        'type' => 'required|string|max:255',
        'score' => 'required|numeric',
        'max_score' => 'required|numeric',
        'weight' => 'required|numeric',
        'entered_by' => 'required|string|exists:users,id',
        'status' => 'required|string|max:255',
        'entered_at' => 'required|date',
        'validated_at' => 'nullable|date',
    ];
    }
}
