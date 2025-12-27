<?php
declare(strict_types=1);

namespace App\Http\Requests\CourseEnrollment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('course_enrollment');
        return [
            'student_id' => ['sometimes','string','exists:students,id', ],
            'course_id' => ['sometimes','string','exists:courses,id', ],
        ];
    }
}
