<?php

namespace App\Models;

use App\Enums\ModePaiement;
use App\Enums\ModeRemuneration;
use App\Enums\StatutEmploye;
use App\Enums\TypeContrat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employe extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule', 'nom', 'prenom', 'sexe', 'date_naissance', 'telephone', 'adresse', 'numero_cnss',
        'fonction', 'categorie', 'chantier_id', 'type_contrat', 'date_embauche', 'date_fin_contrat',
        'mode_remuneration', 'salaire_base', 'taux_journalier', 'statut', 'mode_paiement', 'numero_compte',
    ];

    protected function casts(): array
    {
        return [
            'type_contrat' => TypeContrat::class,
            'mode_remuneration' => ModeRemuneration::class,
            'statut' => StatutEmploye::class,
            'mode_paiement' => ModePaiement::class,
            'date_naissance' => 'date',
            'date_embauche' => 'date',
            'date_fin_contrat' => 'date',
            'salaire_base' => 'decimal:2',
            'taux_journalier' => 'decimal:2',
        ];
    }

    public function getNomCompletAttribute(): string
    {
        return trim($this->prenom.' '.$this->nom);
    }

    public function chantier(): BelongsTo
    {
        return $this->belongsTo(Chantier::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function pointages(): HasMany
    {
        return $this->hasMany(Pointage::class);
    }

    public function conges(): HasMany
    {
        return $this->hasMany(Conge::class)->orderByDesc('date_debut');
    }

    public function avances(): HasMany
    {
        return $this->hasMany(Avance::class)->orderByDesc('date_avance');
    }

    public function bulletins(): HasMany
    {
        return $this->hasMany(Bulletin::class);
    }

    public function scopeActifs(Builder $query): Builder
    {
        return $query->where('statut', StatutEmploye::Actif);
    }

    public function contratExpireBientot(int $jours = 30): bool
    {
        return $this->date_fin_contrat !== null
            && $this->date_fin_contrat->between(now()->startOfDay(), now()->addDays($jours));
    }
}
