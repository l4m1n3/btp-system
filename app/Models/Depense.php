<?php

namespace App\Models;

use App\Enums\ModePaiement;
use App\Enums\PorteeDepense;
use App\Enums\StatutDepense;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Depense extends Model
{
    use HasFactory;

    protected $fillable = [
        'portee', 'chantier_id', 'tache_id', 'categorie_id', 'fournisseur_id', 'date_depense', 'libelle',
        'montant', 'mode_paiement', 'reference_piece', 'justificatif', 'statut', 'saisi_par',
        'valide_par', 'valide_le', 'motif_rejet', 'source_type', 'source_id',
    ];

    protected function casts(): array
    {
        return [
            'portee' => PorteeDepense::class,
            'statut' => StatutDepense::class,
            'mode_paiement' => ModePaiement::class,
            'date_depense' => 'date',
            'valide_le' => 'datetime',
            'montant' => 'decimal:2',
        ];
    }

    public function chantier(): BelongsTo
    {
        return $this->belongsTo(Chantier::class);
    }

    public function tache(): BelongsTo
    {
        return $this->belongsTo(Tache::class);
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(CategorieDepense::class, 'categorie_id');
    }

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function saisiPar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'saisi_par');
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function scopeValidees(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('statut'), StatutDepense::Validee);
    }

    /** Dépenses visibles : un chef de chantier ne voit que celles de ses chantiers. */
    public function scopeVisiblePour(Builder $query, User $user): Builder
    {
        if ($user->voitTousLesChantiers()) {
            return $query;
        }

        return $query->where($query->qualifyColumn('portee'), PorteeDepense::Chantier)
            ->whereIn($query->qualifyColumn('chantier_id'), $user->chantiersDiriges()->select('id'));
    }

    /** Les justificatifs sont privés : servis par une route authentifiée. */
    public function justificatifUrl(): ?string
    {
        return $this->justificatif ? route('depenses.justificatif', $this) : null;
    }

    public function estModifiable(): bool
    {
        return $this->statut === StatutDepense::EnAttente && $this->source_type === null;
    }
}
