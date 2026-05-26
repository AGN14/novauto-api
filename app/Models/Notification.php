<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'destinataire_id',
        'expediteur_id',
        'titre',
        'message',
        'type',
        'lien',
        'lu',
        'canal',
        'sujet',
        'contenu',
        'statut_envoi',
        'nombre_tentatives',
        'date_envoi',
    ];

    protected $casts = [
        'lu' => 'boolean',
        'date_envoi' => 'datetime',
        'nombre_tentatives' => 'integer',
    ];

    public function destinataire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }

    public function expediteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'expediteur_id');
    }

    public static function creer(int $userId, string $titre, string $message, string $type, ?string $lien = null, ?int $expediteurId = null): self
    {
        return self::create([
            'destinataire_id' => $userId,
            'expediteur_id' => $expediteurId,
            'titre' => $titre,
            'message' => $message,
            'type' => $type,
            'lien' => $lien,
            'lu' => false,
        ]);
    }
}