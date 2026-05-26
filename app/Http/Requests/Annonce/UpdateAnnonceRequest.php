<?php

namespace App\Http\Requests\Annonce;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnnonceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre'               => ['sometimes', 'string', 'min:10', 'max:255'],
            'prix'                => ['sometimes', 'numeric', 'min:1'],
            'montant_reservation' => ['sometimes', 'nullable', 'numeric', 'min:10000'],
            'photos'              => ['sometimes', 'array', 'min:1'],
            'photos.*'            => ['url'],
            'statut'              => ['sometimes', 'in:DISPONIBLE,RESERVEE,VENDUE,EXPIREE'],
            'statut_douanier'     => ['sometimes', 'in:DEDOUANE,EN_TRANSIT'],
            'kilometrage'         => ['sometimes', 'integer', 'min:0'],
            'description'         => ['sometimes', 'nullable', 'string'],
            'ville'               => ['sometimes', 'nullable', 'string', 'max:100'],
        ];
    }
}