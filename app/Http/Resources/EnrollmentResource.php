<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'student_id' => $this->student_id,
            'academic_program_id' => $this->academic_program_id,
            'academic_year_id' => $this->academic_year_id,
            'current_semester' => $this->current_semester,
            'status' => $this->status,
            'enrollment_date' => $this->enrollment_date,
            'registration_fee_paid' => $this->registration_fee_paid,
            'is_scholarship' => $this->is_scholarship,
        ];
    }
}
