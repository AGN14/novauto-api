<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    protected $fillable = [
        'vendeur_id',
        'vehicule_id',
        'titre',
        'prix',
        'montant_reservation',
        'statut',
        'photos',
        'description',
        'equipements',
        'ville',
    ];

    protected $casts = [
        'photos'      => 'array',
        'equipements' => 'array',
        'prix'        => 'decimal:2',
        'montant_reservation' => 'decimal:2',
    ];

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class);
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function reservation()
    {
        return $this->hasOne(Reservation::class);
    }

    public function avis()
    {
        return $this->hasMany(Avis::class);
    }

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class);
    }
}