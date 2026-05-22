<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
        protected $fillable = [
        'acheteur_id',
        'annonce_id',
        'montant_acompte',
        'date_reservation',
        'date_expiration',
        'statut',
        'document_reservation',
    ];

    protected $casts = [
        'montant_acompte' => 'decimal:2',
        'date_reservation' => 'datetime',
        'date_expiration' => 'datetime',
    ];

    public function acheteur()
    {
        return $this->belongsTo(Acheteur::class);
    }

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function paiement()
    {
        return $this->hasOne(Paiement::class);
    }
}