<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marque extends Model
{
    protected $fillable = [
        'nom',
        'pays_origine',
    ];

    public function modeles()
    {
        return $this->hasMany(Modele::class);
    }
}