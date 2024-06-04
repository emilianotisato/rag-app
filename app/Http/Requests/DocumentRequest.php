<?php

namespace App\Http\Requests;

use App\Enums\DocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class DocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', new Enum(DocumentType::class)],
            'file' =>
                [
                    'nullable',
                    'required_if:type,'.DocumentType::PDF->value,
                    'file',
                    'mimes:pdf',
                    'max:10240',
                ],
            'path' => ['nullable', 'required_if:type,'.DocumentType::WEB_PAGE->value, 'url'],
            'content' => ['nullable','required_if:type,'.DocumentType::RAW_TEXT->value, 'string'],

        ];
    }
}
