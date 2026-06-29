<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FicheTravaux extends Model
{
    protected $table = 'fiche_travaux';
    protected $fillable = [
        'fiche_id',
        'designation',
        'niveau_avancement',
        'planning_id',
    ];
    public function fiche()
    {
        return $this->belongsTo(FicheJournaliere::class, 'fiche_id');
    }
    public function planning()
    {
        return $this->belongsTo(Planning::class, 'planning_id');
    }
}
