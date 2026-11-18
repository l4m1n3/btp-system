<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Depense */
class DepenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'portee' => $this->portee->value,
            'chantier_id' => $this->chantier_id,
            'chantier' => $this->whenLoaded('chantier', fn () => $this->chantier?->nom),
            'tache_id' => $this->tache_id,
            'categorie_id' => $this->categorie_id,
            'categorie' => $this->whenLoaded('categorie', fn () => $this->categorie->nom),
            'fournisseur_id' => $this->fournisseur_id,
            'date_depense' => $this->date_depense->toDateString(),
            'libelle' => $this->libelle,
            'montant' => (float) $this->montant,
            'mode_paiement' => $this->mode_paiement->value,
            'reference_piece' => $this->reference_piece,
            'a_justificatif' => (bool) $this->justificatif,
            'statut' => $this->statut->value,
            'statut_libelle' => $this->statut->label(),
            'motif_rejet' => $this->motif_rejet,
            'cree_le' => $this->created_at?->toIso8601String(),
        ];
    }
}
