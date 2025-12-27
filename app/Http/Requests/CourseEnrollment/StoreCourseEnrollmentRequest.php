<?php
declare(strict_types=1);

namespace App\Http\Requests\CourseEnrollment;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
        'student_id' => 'required|string|exists:students,id',
        'course_id' => 'required|string|exists:courses,id',
    ];
    }
}
