<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'string', 'max:1000'],
            'metadata' => ['nullable', 'array'],
            'metadata.*' => ['nullable'],
            // Le statut ne peut être mis à jour que via les endpoints approve/reject
        ];
    }

    public function messages(): array
    {
        return [
            'notes.string' => 'Les notes doivent être du texte',
            'notes.max' => 'Les notes ne doivent pas dépasser 1000 caractères',
            'metadata.array' => 'Les métadonnées doivent être un tableau',
        ];
    }
}
