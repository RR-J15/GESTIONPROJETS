<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:150'],
            'contact_name' => ['nullable', 'string', 'max:150'],
            'email'        => ['nullable', 'email', 'max:150', 'unique:clients,email'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'address'      => ['nullable', 'string', 'max:500'],
            'notes'        => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Le nom de la société est obligatoire.',
            'company_name.max'      => 'Le nom de la société ne peut pas dépasser 150 caractères.',
            'email.email'           => 'L\'adresse email n\'est pas valide.',
            'email.unique'          => 'Cette adresse email est déjà utilisée par un autre client.',
        ];
    }
}
