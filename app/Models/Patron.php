<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patron extends Model
{
    protected $table = 'entreprises';

   protected $fillable = [
        'nom',
        'email',
        'telephone',
        'abonnement',
        'date_debut',
        'date_fin',
        'actif'

    ];

    public function chantiers()
    {
        return $this->hasMany(Chantier::class, 'entreprise_id');
    }
}
