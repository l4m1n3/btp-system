<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chantier extends Model
{
     protected $fillable = [
        'nom',
        'description',
        'statut',
        'adresse',
        'budget_total',
        'entreprise_id'
    ];
    
    public function entreprise()
    {
        return $this->belongsTo(Patron::class);
    }
    public function depenses()
    {
        return $this->hasMany(Depense::class);
    }
}