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
    
    public function entreprises()
    {
        return $this->belongsTo(Patron::class, 'entreprise_id');
    }
    public function depenses()
    {
        return $this->hasMany(Depense::class);
    }
    public function devis()
    {
        return $this->hasMany(Devis::class);
    }
}