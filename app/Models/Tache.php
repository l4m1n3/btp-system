<?php

namespace App\Models;

use App\Enums\StatutTache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Tache extends Model
{
    protected $fillable = [
        'chantier_id', 'libelle', 'responsable', 'date_debut_prevue', 'date_fin_prevue',
        'date_debut_reelle', 'date_fin_reelle', 'cout_prevu', 'avancement', 'statut', 'ordre', 'note',
    ];

    protected function casts(): array
    {
        return [
            'statut' => StatutTache::class,
            'date_debut_prevue' => 'date',
            'date_fin_prevue' => 'date',
            'date_debut_reelle' => 'date',
            'date_fin_reelle' => 'date',
            'cout_prevu' => 'decimal:2',
            'avancement' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // Le statut suit l'avancement pour rester cohérent.
        static::saving(function (Tache $tache) {
            $tache->statut ??= StatutTache::Planifie;
            $tache->avancement = max(0, min(100, (int) $tache->avancement));
            if ($tache->avancement >= 100) {
                $tache->statut = StatutTache::Termine;
                $tache->date_fin_reelle ??= Carbon::today();
            } elseif ($tache->avancement > 0 && $tache->statut === StatutTache::Planifie) {
                $tache->statut = StatutTache::EnCours;
            }
            if ($tache->avancement > 0) {
                $tache->date_debut_reelle ??= Carbon::today();
            }
        });
    }

    public function chantier(): BelongsTo
    {
        return $this->belongsTo(Chantier::class);
    }

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class);
    }

    public function estEnRetard(): bool
    {
        if ($this->statut === StatutTache::Termine) {
            return $this->date_fin_reelle && $this->date_fin_reelle->gt($this->date_fin_prevue);
        }

        return $this->date_fin_prevue->lt(Carbon::today());
    }

    public function scopeEnRetard(Builder $query): Builder
    {
        return $query->where('statut', '!=', StatutTache::Termine->value)
            ->whereDate('date_fin_prevue', '<', Carbon::today());
    }
}
