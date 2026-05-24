<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RendezVous extends Model
{
    protected $table = 'rendez_vous';

    protected $fillable = [
        'acheteur_id',
        'annonce_id',
        'date_rdv',
        'heure_rdv',
        'message',
        'message_vendeur',
        'statut',
    ];

    protected $casts = [
        'date_rdv' => 'date',
        'heure_rdv' => 'datetime:H:i',
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