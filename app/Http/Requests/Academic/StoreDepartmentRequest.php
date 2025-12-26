<?php

namespace App\Http\Requests\Academic;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'faculty_id' => ['required', 'uuid', 'exists:faculties,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50'],
            'head_id' => ['nullable', 'uuid', 'exists:users,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
