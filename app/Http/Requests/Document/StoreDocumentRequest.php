<?php

namespace App\Http\Requests\Document;

use App\Enums\DocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'uuid'],
            'type' => ['required', 'string', Rule::in(DocumentType::values())],
            'notes' => ['nullable', 'string', 'max:1000'],
            'document_file' => ['required', 'file'],
            // Note: La validation du fichier sera faite dans le service
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'L\'identifiant de l\'étudiant est requis',
            'student_id.uuid' => 'L\'identifiant doit être un UUID valide',
            'type.required' => 'Le type de document est requis',
            'type.in' => 'Le type de document n\'est pas valide',
            'notes.string' => 'Les notes doivent être du texte',
            'notes.max' => 'Les notes ne doivent pas dépasser 1000 caractères',
            'document_file.required' => 'Un fichier est requis',
            'document_file.file' => 'Le fichier n\'est pas valide',
        ];
    }

    public function attributes(): array
    {
        return [
            'student_id' => 'identifiant étudiant',
            'type' => 'type de document',
            'notes' => 'notes',
            'document_file' => 'fichier document',
        ];
    }
}
