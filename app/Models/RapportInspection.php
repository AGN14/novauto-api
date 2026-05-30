<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RapportInspection extends Model
{
    protected $table = 'rapports_inspection';

    protected $fillable = [
        'annonce_id',
        'vehicule_id',
        'garage_id',
        'date_rdv',
        'heure_rdv',
        'code_presence',
        'code_expire_at',
        'presence_confirmee',
        'code_genere_at',
        'statut',
        'date_soumission',
        'date_inspection',
        'etat_carrosserie',
        'etat_moteur',
        'etat_freins',
        'etat_pneus',
        'kilometrage_verifie',
        'observations',
        'photos_inspection',
        'date_validation',
    ];

    protected $casts = [
        'date_inspection' => 'datetime',
        'date_soumission' => 'datetime',
        'date_validation' => 'datetime',
        'code_expire_at' => 'datetime',
        'code_genere_at' => 'datetime',
        'presence_confirmee' => 'boolean',
        'kilometrage_verifie' => 'integer',
        'photos_inspection' => 'array',
    ];

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function garage()
    {
        return $this->belongsTo(GaragePartenaire::class, 'garage_id');
    }
}