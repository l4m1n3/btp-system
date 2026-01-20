<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevisLigne extends Model
{
   protected $fillable = [
        'devis_id',
        'designation',
        'unite',
        'quantite',
        'prix_unitaire',
        'montant',
    ];

    protected $casts = [
        'quantite'      => 'float',
        'prix_unitaire' => 'float',
        'montant'       => 'float',
    ];

    /**
     * Une ligne appartient à un devis
     */
    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }
}
