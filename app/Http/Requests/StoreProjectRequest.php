<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id'   => ['nullable', 'exists:clients,id'],
            'quote_id'    => ['nullable', 'exists:quotes,id'],
            'name'        => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status'      => ['required', 'in:brouillon,en_cours,en_pause,termine'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'after_or_equal:start_date'],
            'budget'      => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'Le nom du projet est obligatoire.',
            'start_date.required'    => 'La date de début est obligatoire.',
            'end_date.required'      => 'La date de fin est obligatoire.',
            'end_date.after_or_equal'=> 'La date de fin doit être après ou égale à la date de début.',
            'client_id.exists'       => 'Ce client n\'existe pas.',
            'quote_id.exists'        => 'Ce devis n\'existe pas.',
        ];
    }
}