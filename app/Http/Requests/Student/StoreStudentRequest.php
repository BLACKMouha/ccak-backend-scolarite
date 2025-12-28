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
        'user_id' => 'required|string',
        'student_number' => 'required|string',
        'full_name' => 'required|string',
        'date_of_birth' => 'nullable|date',
        'place_of_birth' => 'nullable|string',
        'nationality' => 'nullable|string',
        'phone' => 'nullable|string',
        'emergency_contact_name' => 'nullable|string',
        'emergency_contact_phone' => 'nullable|string',
        'address' => 'nullable|string',
        'photo_url' => 'nullable|string',
        'status' => 'required|string',
    ];
    }
}
