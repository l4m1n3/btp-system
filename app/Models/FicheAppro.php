<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FicheAppro extends Model
{
    
    // protected $table = 'fiche_appros';
    protected $fillable = [
        'fiche_id',
        'designation',
        'unite',
        'quantite',
    ];
     
    public function fiche()
    {
        return $this->belongsTo(FicheJournaliere::class, 'fiche_id');
    }
}
