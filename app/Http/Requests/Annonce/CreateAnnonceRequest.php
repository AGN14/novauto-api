<?php

namespace App\Http\Requests\Annonce;

use Illuminate\Foundation\Http\FormRequest;

class CreateAnnonceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre'       => ['required', 'string', 'min:10', 'max:255'],
            'prix'        => ['required', 'numeric', 'min:1'],
            'photos'      => ['required', 'array', 'min:1'],
            'photos.*'    => ['required', 'url'],
            'vin'         => ['required', 'string', 'size:17'],
            'annee'       => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'kilometrage' => ['required', 'integer', 'min:0'],
            'modele_id'   => ['required', 'exists:modeles,id'],
            'statut_douanier' => ['required', 'in:DEDOUANE,EN_TRANSIT'],
            'description'  => ['nullable', 'string', 'min:20'],
            'equipements'  => ['nullable', 'array'],
            'equipements.*'=> ['string'],
            'ville'        => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required'        => 'Le titre est obligatoire.',
            'titre.min'             => 'Le titre doit contenir au moins 10 caractères.',
            'prix.required'         => 'Le prix est obligatoire.',
            'prix.min'              => 'Le prix doit être supérieur à 0.',
            'photos.required'       => 'Au moins une photo est obligatoire.',
            'photos.min'            => 'Au moins une photo est obligatoire.',
            'vin.required'          => 'Le numéro VIN est obligatoire.',
            'vin.size'              => 'Le numéro VIN doit contenir exactement 17 caractères.',
            'annee.required'        => 'L\'année est obligatoire.',
            'kilometrage.required'  => 'Le kilométrage est obligatoire.',
            'modele_id.required'    => 'Le modèle est obligatoire.',
            'modele_id.exists'      => 'Le modèle sélectionné est invalide.',
            'statut_douanier.required' => 'Le statut douanier est obligatoire.',
            'statut_douanier.in'    => 'Le statut douanier est invalide.',
        ];
    }
}