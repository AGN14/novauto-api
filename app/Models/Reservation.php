<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'acheteur_id',
        'annonce_id',
        'montant_paye',
        'statut',
    ];

    protected $casts = [
        'montant_paye' => 'decimal:2',
    ];

    public function acheteur(): BelongsTo
    {
        return $this->belongsTo(Acheteur::class);
    }

    public function annonce(): BelongsTo
    {
        return $this->belongsTo(Annonce::class);
    }
}