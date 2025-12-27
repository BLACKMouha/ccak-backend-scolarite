<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'student_number' => $this->student_number,
            'full_name' => $this->full_name,
        ];
    }
}
