<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'status'      => ['required', 'in:a_faire,en_cours,en_revision,termine'],
            'priority'    => ['required', 'in:basse,normale,haute,critique'],
            'due_date'    => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'     => 'Le titre de la tâche est obligatoire.',
            'assigned_to.exists' => 'Cet utilisateur n\'existe pas.',
            'due_date.date'      => 'La date d\'échéance n\'est pas valide.',
        ];
    }
}