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

        protected $casts = [
                'date_depense' => 'date',
                'montant' => 'float',
        ];
        public function chantier()
        {
                return $this->belongsTo(Chantier::class);
        }
}
