<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'creator' => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ],
            'title' => $this->title,
            'content' => $this->content,
            'priority' => $this->priority,
            'target_audience' => $this->target_audience,
            'publish_at' => $this->publish_at?->toISOString(),
            'expire_at' => $this->expire_at?->toISOString(),
            'is_draft' => $this->is_draft,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
