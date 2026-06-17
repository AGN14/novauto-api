<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class GaragePartenaire extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'garages_partenaires';

    protected $fillable = [
        'nom',
        'adresse',
        'telephone',
        'ville',
        'email',
        'password',
        'agree',
        'certifie',
        'date_agrement',
        'date_certification',
        'prix_inspection',
        'photo_profil',
        'statut_demande',
        'message_demande',
        'latitude',
        'longitude',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'agree'              => 'boolean',
        'certifie'           => 'boolean',
        'date_agrement'      => 'datetime',
        'date_certification' => 'datetime',
        'prix_inspection'    => 'decimal:2',
        'password'           => 'hashed',
        'latitude'           => 'decimal:7',
        'longitude'          => 'decimal:7',
    ];

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function rapportsInspection()
    {
        return $this->hasMany(RapportInspection::class, 'garage_id');
    }

    public function disponibilites()
    {
        return $this->hasMany(DisponibiliteGarage::class, 'garage_id');
    }
}