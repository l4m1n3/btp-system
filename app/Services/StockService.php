<?php

namespace App\Services;

use App\Enums\TypeMouvement;
use App\Models\Article;
use App\Models\MouvementStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Tient le stock à jour avec un coût moyen unitaire pondéré (CMUP).
 * Toute écriture de mouvement passe par ici pour garder stock_actuel cohérent.
 */
class StockService
{
    public function entree(Article $article, float $quantite, float $prixUnitaire, array $attributs = []): MouvementStock
    {
        $this->verifierQuantite($quantite);

        return DB::transaction(function () use ($article, $quantite, $prixUnitaire, $attributs) {
            $article = Article::lockForUpdate()->findOrFail($article->id);
            $stock = (float) $article->stock_actuel;
            $nouveauStock = $stock + $quantite;
            $article->cout_moyen = $nouveauStock > 0
                ? round(($stock * (float) $article->cout_moyen + $quantite * $prixUnitaire) / $nouveauStock, 2)
                : $prixUnitaire;
            $article->stock_actuel = $nouveauStock;
            $article->save();

            return $article->mouvements()->create([
                ...$attributs,
                'type' => TypeMouvement::Entree,
                'quantite' => $quantite,
                'prix_unitaire' => $prixUnitaire,
                'chantier_id' => null,
            ]);
        });
    }

    /** Sortie de matériaux vers un chantier, valorisée au coût moyen. */
    public function sortie(Article $article, float $quantite, int $chantierId, array $attributs = []): MouvementStock
    {
        $this->verifierQuantite($quantite);

        return DB::transaction(function () use ($article, $quantite, $chantierId, $attributs) {
            $article = Article::lockForUpdate()->findOrFail($article->id);
            if ($quantite > (float) $article->stock_actuel) {
                throw ValidationException::withMessages([
                    'quantite' => "Stock insuffisant : {$article->stock_actuel} {$article->unite} disponible(s).",
                ]);
            }
            $article->stock_actuel = (float) $article->stock_actuel - $quantite;
            $article->save();

            return $article->mouvements()->create([
                ...$attributs,
                'type' => TypeMouvement::Sortie,
                'quantite' => $quantite,
                'prix_unitaire' => $article->cout_moyen,
                'chantier_id' => $chantierId,
                'fournisseur_id' => null,
            ]);
        });
    }

    /** Ajustement d'inventaire : $ecart positif (surplus) ou négatif (manquant). */
    public function ajustement(Article $article, float $ecart, array $attributs = []): MouvementStock
    {
        if ($ecart == 0.0) {
            throw ValidationException::withMessages(['quantite' => "L'écart d'inventaire ne peut pas être nul."]);
        }

        return DB::transaction(function () use ($article, $ecart, $attributs) {
            $article = Article::lockForUpdate()->findOrFail($article->id);
            if ((float) $article->stock_actuel + $ecart < 0) {
                throw ValidationException::withMessages(['quantite' => 'Le stock ne peut pas devenir négatif.']);
            }
            $article->stock_actuel = (float) $article->stock_actuel + $ecart;
            $article->save();

            return $article->mouvements()->create([
                ...$attributs,
                'type' => TypeMouvement::Ajustement,
                'quantite' => $ecart,
                'prix_unitaire' => $article->cout_moyen,
                'chantier_id' => null,
            ]);
        });
    }

    private function verifierQuantite(float $quantite): void
    {
        if ($quantite <= 0) {
            throw ValidationException::withMessages(['quantite' => 'La quantité doit être supérieure à 0.']);
        }
    }
}
