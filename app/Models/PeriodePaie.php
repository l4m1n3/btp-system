<?php

namespace App\Models;

use App\Enums\StatutPeriodePaie;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class PeriodePaie extends Model
{
    protected $table = 'periodes_paie';

    protected $fillable = ['mois', 'statut', 'cloturee_le', 'cloturee_par'];

    protected function casts(): array
    {
        return ['statut' => StatutPeriodePaie::class, 'cloturee_le' => 'datetime'];
    }

    public function bulletins(): HasMany
    {
        return $this->hasMany(Bulletin::class);
    }

    public function cloturePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cloturee_par');
    }

    public function debut(): Carbon
    {
        return Carbon::createFromFormat('Y-m-d', $this->mois.'-01')->startOfDay();
    }

    public function fin(): Carbon
    {
        return $this->debut()->endOfMonth();
    }

    public function getLibelleAttribute(): string
    {
        return ucfirst($this->debut()->locale('fr')->translatedFormat('F Y'));
    }

    public function estCloturee(): bool
    {
        return $this->statut === StatutPeriodePaie::Cloturee;
    }
}
