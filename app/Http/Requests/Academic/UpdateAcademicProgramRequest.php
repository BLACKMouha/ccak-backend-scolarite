<?php

namespace App\Http\Requests\Academic;

use App\Models\AcademicProgram;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAcademicProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => ['sometimes', 'uuid', 'exists:departments,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'level' => ['sometimes', Rule::in(AcademicProgram::LEVELS)],
            'duration_semesters' => ['sometimes', 'integer', 'min:1'],
            'total_credits_required' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
