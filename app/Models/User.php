<?php

namespace App\Models;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'nom',
        'email',
        'password',
        'role',
        'tel',
        'mfa_actif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'mfa_actif' => 'boolean',
        'password' => 'hashed',
    ];

    public function vendeur()
    {
        return $this->hasOne(Vendeur::class);
    }

    public function acheteur()
    {
        return $this->hasOne(Acheteur::class);
    }

    public function administrateur()
    {
        return $this->hasOne(Administrateur::class);
    }

    public function isVendeur(): bool
    {
        return $this->role === 'VENDEUR';
    }

    public function isAcheteur(): bool
    {
        return $this->role === 'ACHETEUR';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'ADMINISTRATEUR';
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}