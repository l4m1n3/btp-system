<?php

namespace App\Support;

use App\Models\Depense;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Applique les filtres de la liste des dépenses (query string) pour les exports PDF / Excel.
 */
class FiltreDepenses
{
    public static function requete(Request $request, User $user): Builder
    {
        return Depense::visiblePour($user)
            ->with(['chantier', 'categorie', 'fournisseur'])
            ->when($request->query('recherche'), fn ($q, $r) => $q->whereLike('libelle', "%$r%"))
            ->when($request->query('portee'), fn ($q, $v) => $q->where('portee', $v))
            ->when($request->query('statut'), fn ($q, $v) => $q->where('statut', $v))
            ->when($request->query('chantier'), fn ($q, $v) => $q->where('chantier_id', $v))
            ->when($request->query('categorie'), fn ($q, $v) => $q->where('categorie_id', $v))
            ->when($request->query('du'), fn ($q, $v) => $q->whereDate('date_depense', '>=', $v))
            ->when($request->query('au'), fn ($q, $v) => $q->whereDate('date_depense', '<=', $v))
            ->orderBy('date_depense')->orderBy('id');
    }

    public static function description(Request $request): string
    {
        $morceaux = [];
        if ($v = $request->query('du')) {
            $morceaux[] = 'du '.date_fr($v);
        }
        if ($v = $request->query('au')) {
            $morceaux[] = 'au '.date_fr($v);
        }
        if ($v = $request->query('portee')) {
            $morceaux[] = \App\Enums\PorteeDepense::tryFrom($v)?->label();
        }
        if ($v = $request->query('statut')) {
            $morceaux[] = 'statut : '.\App\Enums\StatutDepense::tryFrom($v)?->label();
        }
        if ($v = $request->query('chantier')) {
            $morceaux[] = 'chantier : '.\App\Models\Chantier::find($v)?->nom;
        }

        return $morceaux ? implode(' · ', array_filter($morceaux)) : 'Toutes les dépenses';
    }
}
