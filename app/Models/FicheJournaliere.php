<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FicheJournaliere extends Model
{
    protected $table = 'fiches_journalieres';
    protected $fillable = [
        'chantier_id',
        'date',
        'observations',
        'evenements',
        'controleur_nom',
        'controleur_signature', 
        'entreprise_nom',
        'entreprise_signature',
    ];
    protected $casts = [
        'date' => 'date',
    ];

    public function chantier()
    {
        return $this->belongsTo(Chantier::class);
    }
    public function personnel()
    {
        return $this->hasMany(FichePersonnel::class, 'fiche_id');
    }

    public function materiel()
    {
        return $this->hasMany(FicheMateriel::class, 'fiche_id');
    }

    public function appros()
    {
        return $this->hasMany(FicheAppro::class, 'fiche_id');
    }

    public function travaux()
    {
        return $this->hasMany(FicheTravaux::class, 'fiche_id');
    }
}
