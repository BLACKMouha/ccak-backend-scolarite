<?php
declare(strict_types=1);

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('student');
        return [
            'student_number' => ['sometimes','string','max:255', \Illuminate\Validation\Rule::unique('students', 'student_number')->ignore($id)],
            'full_name' => ['sometimes','string','max:255', ],
        ];
    }
}
