<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;

class CreateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'annonce_id'       => ['required', 'exists:annonces,id'],
            'montant'          => ['nullable', 'numeric', 'min:0'],
            'type_reservation' => ['required', 'in:VISITE,ACOMPTE'],
            'message'          => ['nullable', 'string', 'max:500'],
        ];
    }
}