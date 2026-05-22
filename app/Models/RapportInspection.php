<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RapportInspection extends Model
{
    protected $table = 'rapports_inspection';

    protected $fillable = [
        'vehicule_id',
        'garage_id',
        'statut',
        'date_soumission',
        'date_inspection',
        'etat_carrosserie',
        'etat_moteur',
        'etat_freins',
        'etat_pneus',
        'kilometrage_verifie',
        'observations',
        'date_validation',
    ];

    protected $casts = [
        'date_inspection' => 'datetime',
        'date_soumission' => 'datetime',
        'date_validation' => 'datetime',
        'kilometrage_verifie' => 'integer',
    ];

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function garage()
    {
        return $this->belongsTo(GaragePartenaire::class, 'garage_id');
    }
}