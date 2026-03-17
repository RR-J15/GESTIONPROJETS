<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id'       => ['required', 'exists:clients,id'],
            'title'           => ['required', 'string', 'max:200'],
            'issue_date'      => ['required', 'date'],
            'expiry_date'     => ['required', 'date', 'after:issue_date'],
            'tax_rate'        => ['required', 'numeric', 'min:0', 'max:100'],
            'discount_global' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes'           => ['nullable', 'string', 'max:1000'],

            // Lignes du devis
            'items'                => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:300'],
            'items.*.quantity'    => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price'  => ['required', 'numeric', 'min:0'],
            'items.*.discount'    => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required'          => 'Veuillez sélectionner un client.',
            'client_id.exists'            => 'Ce client n\'existe pas.',
            'title.required'              => 'Le titre est obligatoire.',
            'issue_date.required'         => 'La date d\'émission est obligatoire.',
            'expiry_date.required'        => 'La date d\'expiration est obligatoire.',
            'expiry_date.after'           => 'La date d\'expiration doit être après la date d\'émission.',
            'items.required'              => 'Le devis doit contenir au moins une ligne.',
            'items.*.description.required'=> 'La description de chaque ligne est obligatoire.',
            'items.*.quantity.required'   => 'La quantité est obligatoire.',
            'items.*.unit_price.required' => 'Le prix unitaire est obligatoire.',
        ];
    }
}