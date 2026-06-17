<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Vendeur extends Model
{
    protected $fillable = [
        'user_id',
        'type_compte',
        'type_structure',
        'certifie',
        'date_certification',
        'ifu',
        'nom_structure',
        'adresse_structure',
        'rccm',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'certifie'           => 'boolean',
        'date_certification' => 'datetime',
        'latitude'           => 'decimal:7',
        'longitude'          => 'decimal:7',
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