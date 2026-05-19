<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GaragePartenaire extends Model
{
    protected $fillable = [
        'nom',
        'adresse',
        'telephone',
        'ville',
        'agree',
        'date_agrement',
    ];

    protected $casts = [
        'agree' => 'boolean',
        'date_agrement' => 'datetime',
    ];

    public function rapportsInspection()
    {
        return $this->hasMany(RapportInspection::class, 'garage_id');
    }
}