<?php

namespace App\Models;

use App\Enums\StatutConge;
use App\Enums\TypeConge;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conge extends Model
{
    protected $fillable = ['employe_id', 'type', 'date_debut', 'date_fin', 'motif', 'statut', 'traite_par'];

    protected function casts(): array
    {
        return [
            'type' => TypeConge::class,
            'statut' => StatutConge::class,
            'date_debut' => 'date',
            'date_fin' => 'date',
        ];
    }

    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class);
    }

    public function getNombreJoursAttribute(): int
    {
        return (int) $this->date_debut->diffInDays($this->date_fin) + 1;
    }
}
