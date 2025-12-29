<?php

namespace App\Http\Requests\Notification;

use App\Models\Announcement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('ADMIN');
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'string'],
            'priority' => ['sometimes', 'string', Rule::in(Announcement::getPriorities())],
            'target_audience' => ['sometimes', 'array'],
            'publish_at' => ['sometimes', 'nullable', 'date'],
            'expire_at' => ['sometimes', 'nullable', 'date'],
            'is_draft' => ['sometimes', 'boolean'],
        ];
    }
}
