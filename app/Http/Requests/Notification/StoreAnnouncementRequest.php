<?php

namespace App\Http\Requests\Notification;

use App\Models\Announcement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('ADMIN');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'priority' => ['nullable', 'string', Rule::in(Announcement::getPriorities())],
            'target_audience' => ['nullable', 'array'],
            'target_audience.roles' => ['nullable', 'array'],
            'target_audience.classes' => ['nullable', 'array'],
            'target_audience.user_ids' => ['nullable', 'array'],
            'publish_at' => ['nullable', 'date'],
            'expire_at' => ['nullable', 'date', 'after:publish_at'],
            'is_draft' => ['nullable', 'boolean'],
        ];
    }
}
