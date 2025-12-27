<?php
declare(strict_types=1);

namespace App\Http\Requests\Grade;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreGradeRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'course_enrollment_id' => 'required|string|exists:course_enrollments,id',
            'student_id' => 'required|string|exists:students,id',
            'course_id' => 'required|string|exists:courses,id',
            'type' => 'required|string|in:CC,EXAM,TP,ORAL',
            'score' => 'required|numeric|min:0',
            'max_score' => 'required|numeric|min:0|gt:0',
            'weight' => 'required|numeric|min:0',
        ];
    }

    /**
     * Configure the validator instance
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $data = $validator->getData();

            // Validate score is within range (0 to max_score)
            if (isset($data['score'], $data['max_score']) && $data['score'] > $data['max_score']) {
                $validator->errors()->add(
                    'score',
                    "The score cannot exceed the maximum score of {$data['max_score']}."
                );
            }

            // Validate course enrollment consistency
            if (isset($data['course_enrollment_id'], $data['student_id'], $data['course_id'])) {
                $enrollment = \App\Models\CourseEnrollment::where('id', $data['course_enrollment_id'])
                    ->where('student_id', $data['student_id'])
                    ->where('course_id', $data['course_id'])
                    ->first();

                if (!$enrollment) {
                    $validator->errors()->add(
                        'course_enrollment_id',
                        'The course enrollment does not match the provided student and course.'
                    );
                }
            }

            // Validate no duplicate grade type for the same enrollment
            if (isset($data['course_enrollment_id'], $data['type'])) {
                $existingGrade = \App\Models\Grade::where('course_enrollment_id', $data['course_enrollment_id'])
                    ->where('type', $data['type'])
                    ->first();

                if ($existingGrade) {
                    $validator->errors()->add(
                        'type',
                        "A grade of type {$data['type']} already exists for this course enrollment."
                    );
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors
     */
    public function messages(): array
    {
        return [
            'type.in' => 'The grade type must be one of: CC (Continuous Assessment), EXAM, TP (Practical Work), or ORAL.',
            'score.min' => 'The score must be at least 0.',
            'max_score.gt' => 'The maximum score must be greater than 0.',
            'weight.min' => 'The weight must be at least 0.',
            'weight.max' => 'The weight cannot exceed 1 (100%).',
        ];
    }

    /**
     * Handle a failed authorization attempt
     */
    protected function failedAuthorization(): void
    {
        abort(403, 'Only faculty members are authorized to create grades.');
    }
}
