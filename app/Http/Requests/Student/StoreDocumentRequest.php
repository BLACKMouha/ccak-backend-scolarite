<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document' => [
                'required',
                'file',
                'max:5120', // 5MB max
                'mimes:pdf,jpg,jpeg,png',
            ],
            'type' => [
                'required',
                'in:CNI,BIRTH_CERT,BAC_DIPLOMA,TRANSCRIPT,PHOTO,MEDICAL',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'document.required' => 'Le fichier du document est requis.',
            'document.file' => 'Le document doit être un fichier valide.',
            'document.max' => 'Le fichier ne doit pas dépasser 5 Mo.',
            'document.mimes' => 'Le document doit être au format PDF, JPG, JPEG ou PNG.',
            'type.required' => 'Le type de document est requis.',
            'type.in' => 'Le type de document doit être l\'un des suivants : CNI, BIRTH_CERT, BAC_DIPLOMA, TRANSCRIPT, PHOTO, MEDICAL.',
        ];
    }
}
