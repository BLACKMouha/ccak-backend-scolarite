<?php

namespace App\Http\Requests\Academic;

use App\Models\AcademicProgram;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAcademicProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => ['required', 'uuid', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'level' => ['required', Rule::in(AcademicProgram::LEVELS)],
            'duration_semesters' => ['required', 'integer', 'min:1'],
            'total_credits_required' => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
