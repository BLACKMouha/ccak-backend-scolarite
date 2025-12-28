<?php

namespace App\Http\Resources\Academic;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliberationSessionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'academic_program_id' => $this->academic_program_id,
            'academic_program' => new AcademicProgramResource($this->whenLoaded('academicProgram')),
            'academic_year_id' => $this->academic_year_id,
            'academic_year' => new AcademicYearResource($this->whenLoaded('academicYear')),
            'semester' => $this->semester,
            'session_name' => $this->session_name,
            'session_date' => $this->session_date?->toDateString(),
            'status' => $this->status,
            'presided_by' => $this->presided_by,
            'president' => $this->whenLoaded('president'),
            'jury_members' => $this->jury_members ?? [],
            'results' => $this->whenLoaded('results'),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
