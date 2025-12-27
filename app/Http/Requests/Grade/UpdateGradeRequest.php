<?php
declare(strict_types=1);

namespace App\Http\Requests\Grade;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Get the grade being updated
        $grade = \App\Models\Grade::findOrFail($this->route('grade'));

        // Only allow updates if status is DRAFT
        if ($grade->status !== 'DRAFT') {
            return false;
        }

        // Only the original creator can update the grade
        return $grade->entered_by === auth()->id;
    }

    public function rules(): array
    {
        return [
            'course_enrollment_id' => 'sometimes|string|exists:course_enrollments,id',
            'student_id' => 'sometimes|string|exists:students,id',
            'course_id' => 'sometimes|string|exists:courses,id',
            'type' => 'sometimes|string|in:CC,EXAM,TP,ORAL',
            'score' => 'sometimes|numeric|min:0',
            'max_score' => 'sometimes|numeric|min:0|gt:0',
            'weight' => 'sometimes|numeric|min:0|max:1',
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

            // If only score is provided, check against existing max_score
            $gradeId = $this->route('grade');
            $grade = \App\Models\Grade::find($gradeId);

            if ($grade && isset($data['score']) && !isset($data['max_score'])) {
                if ($data['score'] > $grade->max_score) {
                    $validator->errors()->add(
                        'score',
                        "The score cannot exceed the maximum score of {$grade->max_score}."
                    );
                }
            }

            // If only max_score is provided, check against existing score
            if ($grade && isset($data['max_score']) && !isset($data['score'])) {
                if ($grade->score > $data['max_score']) {
                    $validator->errors()->add(
                        'max_score',
                        "The maximum score cannot be less than the current score of {$grade->score}."
                    );
                }
            }

            // Validate course enrollment consistency if any of these fields are being updated
            if (isset($data['course_enrollment_id']) || isset($data['student_id']) || isset($data['course_id'])) {
                $enrollmentId = $data['course_enrollment_id'] ?? $grade->course_enrollment_id;
                $studentId = $data['student_id'] ?? $grade->student_id;
                $courseId = $data['course_id'] ?? $grade->course_id;

                $enrollment = \App\Models\CourseEnrollment::where('id', $enrollmentId)
                    ->where('student_id', $studentId)
                    ->where('course_id', $courseId)
                    ->first();

                if (!$enrollment) {
                    $validator->errors()->add(
                        'course_enrollment_id',
                        'The course enrollment does not match the provided student and course.'
                    );
                }
            }

            // Validate no duplicate grade type if type is being changed
            if (isset($data['type']) && $grade && $data['type'] !== $grade->type) {
                $existingGrade = \App\Models\Grade::where('course_enrollment_id', $grade->course_enrollment_id)
                    ->where('type', $data['type'])
                    ->where('id', '!=', $grade->id)
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
        $grade = \App\Models\Grade::find($this->route('grade'));

        if ($grade && $grade->status !== 'DRAFT') {
            abort(403, 'Grades can only be updated when in DRAFT status.');
        }

        if ($grade && $grade->entered_by !== auth()->id) {
            abort(403, 'Only the original creator can update this grade.');
        }

        abort(403, 'You are not authorized to update grades.');
    }
}
