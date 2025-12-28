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
            'user_id' => ['sometimes','string', \Illuminate\Validation\Rule::unique('students', 'user_id')->ignore($id)],
            'student_number' => ['sometimes','string', \Illuminate\Validation\Rule::unique('students', 'student_number')->ignore($id)],
            'full_name' => ['sometimes','string', ],
            'date_of_birth' => ['nullable','date', ],
            'place_of_birth' => ['nullable','string', ],
            'nationality' => ['nullable','string', ],
            'phone' => ['nullable','string', ],
            'emergency_contact_name' => ['nullable','string', ],
            'emergency_contact_phone' => ['nullable','string', ],
            'address' => ['nullable','string', ],
            'photo_url' => ['nullable','string', ],
            'status' => ['sometimes','string', ],
        ];
    }
}
