<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FicheMateriel extends Model
{

    protected $fillable = [
        'fiche_id',
        'designation',
        'nombre',
        'etat',
    ];

    public function fiche()
    {
        return $this->belongsTo(FicheJournaliere::class, 'fiche_id');
    }
}
