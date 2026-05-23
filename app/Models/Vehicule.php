<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
    protected $fillable = [
        'modele_id',
        'vin',
        'annee',
        'kilometrage',
        'statut_douanier',
        'vin_verifie',
    ];

    protected $casts = [
        'vin_verifie' => 'boolean',
        'annee' => 'integer',
        'kilometrage' => 'integer',
    ];

    public function modele()
    {
        return $this->belongsTo(Modele::class);
    }

    public function annonce()
    {
        return $this->hasOne(Annonce::class);
    }

    public function annonces()
    {
        return $this->hasMany(Annonce::class);
    }

    public function rapportInspection()
    {
        return $this->hasOne(RapportInspection::class);
    }
}