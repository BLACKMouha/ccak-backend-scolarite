<?php

namespace App\Http\Requests\Academic;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $department = $this->route('department');
        $facultyId = $this->input('faculty_id') ?: ($department?->faculty_id);

        return [
            'faculty_id' => ['sometimes', 'uuid', 'exists:faculties,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('departments', 'code')
                    ->where('faculty_id', $facultyId)
                    ->ignore($department),
            ],
            'head_id' => ['nullable', 'uuid', 'exists:users,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
