<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acheteur extends Model
{
    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class);
    }

    public function avis()
    {
        return $this->hasMany(Avis::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }
}