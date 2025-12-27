<?php
declare(strict_types=1);

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
        'student_number' => 'required|string|max:255|unique:students,student_number',
        'full_name' => 'required|string|max:255',
    ];
    }
}
