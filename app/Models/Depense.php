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
                'date_depense',
                'planning_id',
        ];

        protected $casts = [
                'date_depense' => 'date',
                'montant' => 'float',
        ];
        public function chantier()
        {
                return $this->belongsTo(Chantier::class);
        }
        public function planning()
        {
                return $this->belongsTo(Planning::class);
        }
}
