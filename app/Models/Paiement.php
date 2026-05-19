<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'reservation_id',
        'acheteur_id',
        'montant',
        'moyen',
        'statut',
        'reference_externe',
        'date_transaction',
        'recu',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_transaction' => 'datetime',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function acheteur()
    {
        return $this->belongsTo(Acheteur::class);
    }
}