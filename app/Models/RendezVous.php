<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    protected $table = 'rendez_vous';

    protected $fillable = [
        'acheteur_id',
        'annonce_id',
        'disponibilite_id',
        'date_heure',
        'lieu',
        'statut',
        'motif_annulation',
    ];

    protected $casts = [
        'date_heure' => 'datetime',
    ];

    public function acheteur()
    {
        return $this->belongsTo(Acheteur::class);
    }

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function disponibilite()
    {
        return $this->belongsTo(Disponibilite::class);
    }
}