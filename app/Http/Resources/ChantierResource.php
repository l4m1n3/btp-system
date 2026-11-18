<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Chantier */
class ChantierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'nom' => $this->nom,
            'localisation' => $this->localisation,
            'statut' => $this->statut->value,
            'statut_libelle' => $this->statut->label(),
            'marche' => $this->whenLoaded('marche', fn () => $this->marche ? ['id' => $this->marche->id, 'reference' => $this->marche->reference, 'intitule' => $this->marche->intitule] : null),
            'chef_chantier' => $this->whenLoaded('chef', fn () => $this->chef?->name),
            'date_debut' => $this->date_debut?->toDateString(),
            'date_fin_prevue' => $this->date_fin_prevue?->toDateString(),
            'en_retard' => $this->estEnRetard(),
            'avancement' => $this->avancement,
            'budget' => (float) $this->budget,
            'cout_engage' => $this->coutTotal(),
            'taux_consommation_budget' => $this->tauxConsommationBudget(),
            'taches' => $this->whenLoaded('taches', fn () => $this->taches->map(fn ($t) => [
                'id' => $t->id,
                'libelle' => $t->libelle,
                'date_debut_prevue' => $t->date_debut_prevue->toDateString(),
                'date_fin_prevue' => $t->date_fin_prevue->toDateString(),
                'cout_prevu' => (float) $t->cout_prevu,
                'avancement' => $t->avancement,
                'statut' => $t->statut->value,
                'en_retard' => $t->estEnRetard(),
            ])),
        ];
    }
}
