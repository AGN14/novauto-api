<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendeur extends Model
{
    protected $fillable = [
        'user_id',
        'type_compte',
        'certifie',
        'date_certification',
        'ifu',
        'nom_structure',
        'adresse_structure',
        'rccm',
    ];

    protected $casts = [
        'certifie' => 'boolean',
        'date_certification' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function annonces()
    {
        return $this->hasMany(Annonce::class);
    }

    public function disponibilites()
    {
        return $this->hasMany(Disponibilite::class);
    }

    public function avis()
    {
        return $this->hasMany(Avis::class);
    }
}