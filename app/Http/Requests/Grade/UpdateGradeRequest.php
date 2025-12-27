<?php
declare(strict_types=1);

namespace App\Http\Requests\Grade;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('grade');
        return [
            'course_enrollment_id' => ['sometimes','string','exists:course_enrollments,id', ],
            'student_id' => ['sometimes','string','exists:students,id', ],
            'course_id' => ['sometimes','string','exists:courses,id', ],
            'type' => ['sometimes','string','max:255', ],
            'score' => ['sometimes','numeric', ],
            'max_score' => ['sometimes','numeric', ],
            'weight' => ['sometimes','numeric', ],
            'entered_by' => ['sometimes','string','exists:users,id', ],
            'status' => ['sometimes','string','max:255', ],
            'entered_at' => ['sometimes','date', ],
            'validated_at' => ['nullable','date', ],
        ];
    }
}
