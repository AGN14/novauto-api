<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modele extends Model
{
    protected $fillable = [
        'marque_id',
        'nom',
        'type_carrosserie',
        'carburant',
        'transmission',
    ];

    public function marque()
    {
        return $this->belongsTo(Marque::class);
    }

    public function vehicules()
    {
        return $this->hasMany(Vehicule::class);
    }
}