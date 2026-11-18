<?php

namespace App\Models;

use App\Enums\EtatMateriel;
use App\Enums\TypeMateriel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materiel extends Model
{
    protected $fillable = [
        'code', 'designation', 'type', 'marque', 'immatriculation', 'date_acquisition',
        'valeur_acquisition', 'etat', 'chantier_id', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => TypeMateriel::class,
            'etat' => EtatMateriel::class,
            'date_acquisition' => 'date',
            'valeur_acquisition' => 'decimal:2',
        ];
    }

    public function chantier(): BelongsTo
    {
        return $this->belongsTo(Chantier::class);
    }

    public function affectations(): HasMany
    {
        return $this->hasMany(AffectationMateriel::class)->orderByDesc('date_debut');
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class)->orderByDesc('date_maintenance');
    }
}
