<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Planning extends Model
{
    protected $fillable = [
        'chantier_id',
        'activite',
        'responsable',
        'note',
        'date_debut_prevue',
        'date_fin_prevue',
        'date_debut_reelle',
        'date_fin_reelle',
        'cout_prevu',
        'cout_reel',
        'statut',
    ];

    protected $casts = [
        'date_debut_prevue'  => 'date',
        'date_fin_prevue'    => 'date',
        'date_debut_reelle'  => 'date',
        'date_fin_reelle'    => 'date',
        'cout_prevu'         => 'decimal:2',
        'cout_reel'          => 'decimal:2',
    ];

    // ─── Relation ────────────────────────────────────────────────

    public function chantier(): BelongsTo
    {
        return $this->belongsTo(Chantier::class);
    }

    // ─── Accesseurs : retard ─────────────────────────────────────

    /**
     * L'activité est-elle en retard ?
     * Vrai si : terminée après la date prévue,
     *           OU non terminée et la date de fin prévue est dépassée.
     */
    public function getEstEnRetardAttribute(): bool
    {
        if ($this->date_fin_reelle) {
            return $this->date_fin_reelle->gt($this->date_fin_prevue);
        }

        return Carbon::now()->gt($this->date_fin_prevue)
            && $this->statut !== 'termine';
    }

    /**
     * Nombre de jours de retard (positif = retard, négatif = avance).
     */
    public function getJoursRetardAttribute(): int
    {
        $reference = $this->date_fin_reelle ?? Carbon::now();

        return (int) $this->date_fin_prevue->diffInDays($reference, false) * -1;
    }

    // ─── Accesseurs : budget ─────────────────────────────────────

    /**
     * L'activité dépasse-t-elle son budget prévu ?
     */
    public function getEstEnDepassementAttribute(): bool
    {
        return $this->cout_reel > $this->cout_prevu;
    }

    /**
     * Montant du dépassement (positif = dépassement, négatif = économie).
     */
    public function getMontantDepassementAttribute(): float
    {
        return (float) ($this->cout_reel - $this->cout_prevu);
    }

    /**
     * Pourcentage d'avancement budgétaire.
     * Ex : 120 → 20% de dépassement / 80 → 20% d'économie.
     */
    public function getPourcentageCoutAttribute(): float
    {
        if ($this->cout_prevu == 0) return 0;

        return round(($this->cout_reel / $this->cout_prevu) * 100, 2);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeEnRetard($query)
    {
        return $query->where(function ($q) {
            // Terminées après la date prévue
            $q->whereNotNull('date_fin_reelle')
                ->whereColumn('date_fin_reelle', '>', 'date_fin_prevue');
        })->orWhere(function ($q) {
            // Non terminées et date dépassée
            $q->whereNull('date_fin_reelle')
                ->where('date_fin_prevue', '<', Carbon::now())
                ->where('statut', '!=', 'termine');
        });
    }
 
    public function scopeEnDepassement($query)
    {
        return $query->whereColumn('cout_reel', '>', 'cout_prevu');
    }

    public function scopeParChantier($query, int $chantierId)
    {
        return $query->where('chantier_id', $chantierId);
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }
    public function depenses()
    {
        return $this->hasMany(Depense::class);
    }
}
