<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseEnrollmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'student_id' => $this->student_id,
            'course_id' => $this->course_id,
        ];
    }
}
