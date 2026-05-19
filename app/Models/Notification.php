<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'destinataire_id',
        'type',
        'canal',
        'sujet',
        'contenu',
        'statut_envoi',
        'nombre_tentatives',
        'date_envoi',
    ];

    protected $casts = [
        'date_envoi' => 'datetime',
        'nombre_tentatives' => 'integer',
    ];

    public function destinataire()
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }
}