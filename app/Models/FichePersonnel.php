<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FichePersonnel extends Model
{
   
    // protected $table = 'fiche_personnels';
    protected $fillable = [
        'fiche_id',
        'designation',
        'hommes',   
        'femmes',
    ];

    public function fiche()
    {
        return $this->belongsTo(FicheJournaliere::class, 'fiche_id');
    }
}
