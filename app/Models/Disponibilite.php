<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disponibilite extends Model
{
    protected $fillable = [
        'vendeur_id',
        'jour',
        'heure_debut',
        'heure_fin',
        'statut',
    ];

    protected $casts = [
        'jour' => 'date',
    ];

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class);
    }

    public function rendezVous()
    {
        return $this->hasOne(RendezVous::class);
    }
}