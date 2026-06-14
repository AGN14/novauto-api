<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'reservation_id',
        'rapport_inspection_id',
        'type',
        'transaction_id',
        'reference',
        'montant',
        'statut',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function rapportInspection()
    {
        return $this->belongsTo(RapportInspection::class, 'rapport_inspection_id');
    }
}