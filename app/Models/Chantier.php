<?php

namespace App\Models;

use App\Enums\StatutChantier;
use App\Enums\StatutDepense;
use App\Enums\TypeMouvement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chantier extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'nom', 'marche_id', 'chef_chantier_id', 'localisation', 'budget', 'statut',
        'date_debut', 'date_fin_prevue', 'date_fin_reelle', 'description',
    ];

    protected function casts(): array
    {
        return [
            'statut' => StatutChantier::class,
            'budget' => 'decimal:2',
            'date_debut' => 'date',
            'date_fin_prevue' => 'date',
            'date_fin_reelle' => 'date',
        ];
    }

    public function marche(): BelongsTo
    {
        return $this->belongsTo(Marche::class);
    }

    public function chef(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chef_chantier_id');
    }

    public function taches(): HasMany
    {
        return $this->hasMany(Tache::class)->orderBy('ordre')->orderBy('date_debut_prevue');
    }

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class);
    }

    public function rapports(): HasMany
    {
        return $this->hasMany(RapportJournalier::class)->orderByDesc('date_rapport');
    }

    public function materiels(): HasMany
    {
        return $this->hasMany(Materiel::class);
    }

    public function employes(): HasMany
    {
        return $this->hasMany(Employe::class);
    }

    public function mouvementsStock(): HasMany
    {
        return $this->hasMany(MouvementStock::class);
    }

    /** Restreint aux chantiers visibles par l'utilisateur (chef de chantier = ses chantiers). */
    public function scopeVisiblePour(Builder $query, User $user): Builder
    {
        return $user->voitTousLesChantiers() ? $query : $query->where('chef_chantier_id', $user->id);
    }

    public function scopeActifs(Builder $query): Builder
    {
        return $query->whereIn('statut', [StatutChantier::EnPreparation, StatutChantier::EnCours, StatutChantier::Suspendu]);
    }

    /**
     * Avancement physique (%) pondéré par le coût prévu des tâches
     * (moyenne simple si aucun coût n'est renseigné).
     */
    public function getAvancementAttribute(): float
    {
        $taches = $this->relationLoaded('taches') ? $this->taches : $this->taches()->get();
        if ($taches->isEmpty()) {
            return $this->statut === StatutChantier::Termine ? 100.0 : 0.0;
        }
        $poids = $taches->sum(fn ($t) => (float) $t->cout_prevu);
        if ($poids <= 0) {
            return round($taches->avg('avancement'), 1);
        }

        return round($taches->sum(fn ($t) => (float) $t->cout_prevu * $t->avancement) / $poids, 1);
    }

    public function totalDepensesValidees(): float
    {
        return (float) $this->depenses()->where('statut', StatutDepense::Validee)->sum('montant');
    }

    public function totalMateriauxConsommes(): float
    {
        return (float) $this->mouvementsStock()->where('type', TypeMouvement::Sortie)
            ->selectRaw('COALESCE(SUM(quantite * prix_unitaire), 0) as total')->value('total');
    }

    /** Coût total engagé sur le chantier = dépenses validées + matériaux sortis du stock. */
    public function coutTotal(): float
    {
        return $this->totalDepensesValidees() + $this->totalMateriauxConsommes();
    }

    public function tauxConsommationBudget(): float
    {
        return (float) $this->budget > 0 ? round($this->coutTotal() / (float) $this->budget * 100, 1) : 0;
    }

    public function estEnRetard(): bool
    {
        return $this->date_fin_prevue
            && $this->statut !== StatutChantier::Termine
            && $this->date_fin_prevue->isPast();
    }
}
