<?php

namespace App\Models;

use App\Enums\Domaine;
use App\Enums\StatutDecompte;
use App\Enums\StatutMarche;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Marche extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'intitule', 'client_id', 'domaine', 'statut', 'localisation',
        'montant_ht', 'taux_tva', 'taux_retenue_garantie', 'avance_demarrage',
        'date_soumission', 'date_signature', 'date_ordre_service', 'delai_jours',
        'date_fin_prevue', 'date_reception', 'description',
    ];

    protected function casts(): array
    {
        return [
            'domaine' => Domaine::class,
            'statut' => StatutMarche::class,
            'montant_ht' => 'decimal:2',
            'taux_tva' => 'decimal:2',
            'taux_retenue_garantie' => 'decimal:2',
            'avance_demarrage' => 'decimal:2',
            'date_soumission' => 'date',
            'date_signature' => 'date',
            'date_ordre_service' => 'date',
            'date_fin_prevue' => 'date',
            'date_reception' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function chantiers(): HasMany
    {
        return $this->hasMany(Chantier::class);
    }

    public function decomptes(): HasMany
    {
        return $this->hasMany(Decompte::class)->orderBy('numero');
    }

    public function getMontantTtcAttribute(): float
    {
        return round((float) $this->montant_ht * (1 + (float) $this->taux_tva / 100), 2);
    }

    /** Travaux facturés (décomptes non rejetés), HT. */
    public function getMontantFactureAttribute(): float
    {
        return (float) $this->decomptes->where('statut', '!=', StatutDecompte::Rejete)->sum('montant_ht');
    }

    public function getMontantEncaisseAttribute(): float
    {
        return (float) $this->decomptes->sum('montant_paye');
    }

    /** Reste à réaliser (HT). */
    public function getResteARealiserAttribute(): float
    {
        return max(0, (float) $this->montant_ht - $this->montant_facture);
    }

    public function getTauxFacturationAttribute(): float
    {
        return (float) $this->montant_ht > 0 ? round($this->montant_facture / (float) $this->montant_ht * 100, 1) : 0;
    }
}
