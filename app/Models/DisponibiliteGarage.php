<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DisponibiliteGarage extends Model
{
    protected $table = 'disponibilites_garage';
    protected $fillable = [
        'garage_id',
        'jour',
        'heure_debut',
        'heure_fin',
        'statut',
    ];
    protected $casts = [
        'jour' => 'date:Y-m-d',
    ];
    public function garage()
    {
        return $this->belongsTo(GaragePartenaire::class, 'garage_id');
    }
}