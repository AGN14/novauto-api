<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'nom'      => ['required', 'string', 'min:2', 'max:255'],
            'email'    => ['required', 'string', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'tel'      => ['required', 'string', 'min:8'],
            'role'     => ['required', 'in:ACHETEUR,VENDEUR'],
        ];

        if ($this->input('role') === 'VENDEUR') {
            $rules['type_compte'] = ['required', 'in:PROFESSIONNEL,PARTICULIER'];
        }

        if ($this->input('type_compte') === 'PROFESSIONNEL') {
            $rules['ifu']               = ['required', 'string', 'unique:vendeurs,ifu'];
            $rules['nom_structure']     = ['required', 'string', 'max:255'];
            $rules['adresse_structure'] = ['required', 'string'];
            $rules['rccm']              = ['required', 'string', 'max:50'];
            $rules['type_structure']    = ['required', 'in:PARC_AUTO,CONCESSIONNAIRE'];
            $rules['latitude']          = ['nullable', 'numeric'];
            $rules['longitude']         = ['nullable', 'numeric'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nom.required'               => 'Le nom est obligatoire.',
            'nom.min'                    => 'Le nom doit contenir au moins 2 caractères.',
            'email.required'             => 'L\'email est obligatoire.',
            'email.email'                => 'Le format de l\'email est invalide.',
            'email.unique'               => 'Cet email est déjà utilisé.',
            'password.required'          => 'Le mot de passe est obligatoire.',
            'password.min'               => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed'         => 'Les mots de passe ne correspondent pas.',
            'tel.required'               => 'Le numéro de téléphone est obligatoire.',
            'role.required'              => 'Le type de compte est obligatoire.',
            'role.in'                    => 'Le type de compte est invalide.',
            'type_compte.required'       => 'Le type de vendeur est obligatoire.',
            'ifu.required'               => 'Le numéro IFU est obligatoire.',
            'ifu.unique'                 => 'Ce numéro IFU est déjà utilisé.',
            'nom_structure.required'     => 'Le nom de la structure est obligatoire.',
            'adresse_structure.required' => 'L\'adresse de la structure est obligatoire.',
            'rccm.required'              => 'Le numéro RCCM est obligatoire.',
            'type_structure.required'    => 'Veuillez sélectionner le type de structure.',
            'type_structure.in'          => 'Le type de structure est invalide.',
        ];
    }
}