<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
    protected $table = 'avis';

    protected $fillable = [
        'acheteur_id',
        'vendeur_id',
        'annonce_id',
        'type',
        'note',
        'commentaire',
        'signale',
        'statut',
    ];

    protected $casts = [
        'signale' => 'boolean',
        'note' => 'integer',
    ];

    public function acheteur()
    {
        return $this->belongsTo(Acheteur::class);
    }

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class);
    }

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }
}