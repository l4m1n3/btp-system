<?php

namespace App\Models; 

use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
        
        protected $fillable = [
        'reference',
        'intitule',
        'montant',
        'date_emission',
        'chantier_id',
    ];

    /**
     * Un devis possède plusieurs lignes
     */
    public function lignes()
    {
        return $this->hasMany(DevisLigne::class);
    }

    /**
     * Total du devis (calculé dynamiquement)
     */
    public function getTotalAttribute()
    {
        return $this->lignes()->sum('montant');
    }
    public function chantier()
    {
        return $this->belongsTo(Chantier::class);
    }
}
