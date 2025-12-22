<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
        protected $fillable = [
        'chantier_id',
        'categorie',
        'description',
        'montant',
        'justificatif',
        'responsable',
        'date_depense'
        ];
}
