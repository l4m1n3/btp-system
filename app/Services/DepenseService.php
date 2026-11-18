<?php

namespace App\Services;

use App\Enums\PorteeDepense;
use App\Enums\StatutDepense;
use App\Models\Depense;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DepenseService
{
    /**
     * Règles de validation communes (web + API).
     *
     * @return array<string, mixed>
     */
    public static function regles(): array
    {
        return [
            'portee' => ['required', 'in:'.implode(',', PorteeDepense::values())],
            'chantier_id' => ['nullable', 'required_if:portee,chantier', 'exists:chantiers,id'],
            'tache_id' => ['nullable', 'exists:taches,id'],
            'categorie_id' => ['required', 'exists:categories_depenses,id'],
            'fournisseur_id' => ['nullable', 'exists:fournisseurs,id'],
            'date_depense' => ['required', 'date', 'before_or_equal:today'],
            'libelle' => ['required', 'string', 'max:255'],
            'montant' => ['required', 'numeric', 'min:1'],
            'mode_paiement' => ['required', 'string'],
            'reference_piece' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Enregistre (ou modifie) une dépense en appliquant les règles métier :
     * cohérence portée/chantier/tâche, droits, justificatif, auto-validation.
     */
    public function enregistrer(array $data, User $auteur, ?UploadedFile $justificatif = null, ?Depense $depense = null): Depense
    {
        $portee = PorteeDepense::from($data['portee']);

        if ($portee === PorteeDepense::Interne) {
            if (Gate::forUser($auteur)->denies('depenses.interne')) {
                throw ValidationException::withMessages(['portee' => "Vous n'êtes pas autorisé à saisir des dépenses internes."]);
            }
            $data['chantier_id'] = null;
            $data['tache_id'] = null;
        } else {
            $chantier = \App\Models\Chantier::findOrFail($data['chantier_id']);
            if (Gate::forUser($auteur)->denies('chantier.voir', $chantier)) {
                throw ValidationException::withMessages(['chantier_id' => 'Ce chantier ne vous est pas attribué.']);
            }
            if (! empty($data['tache_id']) && ! $chantier->taches()->whereKey($data['tache_id'])->exists()) {
                throw ValidationException::withMessages(['tache_id' => "Cette tâche n'appartient pas au chantier choisi."]);
            }
        }

        $categorie = \App\Models\CategorieDepense::findOrFail($data['categorie_id']);
        if ($categorie->portee !== $portee) {
            throw ValidationException::withMessages(['categorie_id' => 'Cette catégorie ne correspond pas au type de dépense.']);
        }

        if ($depense && ! $depense->estModifiable()) {
            throw ValidationException::withMessages(['libelle' => 'Une dépense validée, rejetée ou générée automatiquement ne peut plus être modifiée.']);
        }

        return DB::transaction(function () use ($data, $auteur, $justificatif, $depense) {
            $attributs = collect($data)->only([
                'portee', 'chantier_id', 'tache_id', 'categorie_id', 'fournisseur_id', 'date_depense',
                'libelle', 'montant', 'mode_paiement', 'reference_piece',
            ])->map(fn ($v) => $v === '' ? null : $v)->all();

            if ($justificatif) {
                $attributs['justificatif'] = $justificatif->store('justificatifs/'.now()->format('Y/m'), 'local');
                if ($depense?->justificatif) {
                    Storage::disk('local')->delete($depense->justificatif);
                }
            }

            if ($depense) {
                $depense->update($attributs);
            } else {
                $depense = Depense::create([...$attributs, 'saisi_par' => $auteur->id, 'statut' => StatutDepense::EnAttente]);
            }

            // Les validateurs n'ont pas besoin de se valider eux-mêmes.
            if (Gate::forUser($auteur)->allows('depenses.valider') && $depense->statut === StatutDepense::EnAttente) {
                $this->valider($depense, $auteur);
            }

            return $depense->fresh();
        });
    }

    public function valider(Depense $depense, User $validateur): void
    {
        $this->verifierEnAttente($depense);
        $depense->update([
            'statut' => StatutDepense::Validee,
            'valide_par' => $validateur->id,
            'valide_le' => now(),
            'motif_rejet' => null,
        ]);
    }

    public function rejeter(Depense $depense, User $validateur, string $motif): void
    {
        $this->verifierEnAttente($depense);
        $depense->update([
            'statut' => StatutDepense::Rejetee,
            'valide_par' => $validateur->id,
            'valide_le' => now(),
            'motif_rejet' => $motif,
        ]);
    }

    private function verifierEnAttente(Depense $depense): void
    {
        if ($depense->statut !== StatutDepense::EnAttente) {
            throw ValidationException::withMessages(['statut' => 'Cette dépense a déjà été traitée.']);
        }
    }
}
