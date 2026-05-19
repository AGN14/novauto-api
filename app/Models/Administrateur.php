<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrateur extends Model
{
    protected $fillable = [
        'user_id',
        'niveau_acces',
        'journal_actions',
    ];

    protected $casts = [
        'journal_actions' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}