<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Chantier;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StockController extends Controller
{
    public function articles()
    {
        abort_unless(Gate::any(['logistique.view', 'stock.sortie']), 403);

        return response()->json(['data' => Article::orderBy('designation')->get(['id', 'code', 'designation', 'unite', 'stock_actuel', 'stock_minimum'])]);
    }

    /** POST /api/chantiers/{chantier}/sorties-stock */
    public function sortie(Request $request, Chantier $chantier, StockService $stock)
    {
        abort_unless(Gate::any(['logistique.manage', 'stock.sortie']), 403);
        $this->authorize('chantier.voir', $chantier);
        $data = $request->validate([
            'article_id' => 'required|exists:articles,id',
            'quantite' => 'required|numeric|gt:0',
            'date_mouvement' => 'nullable|date|before_or_equal:today',
            'reference' => 'nullable|string|max:100',
            'observation' => 'nullable|string|max:255',
        ]);
        $mouvement = $stock->sortie(Article::findOrFail($data['article_id']), (float) $data['quantite'], $chantier->id, [
            'date_mouvement' => $data['date_mouvement'] ?? now()->toDateString(),
            'reference' => $data['reference'] ?? null,
            'observation' => $data['observation'] ?? null,
            'saisi_par' => $request->user()->id,
        ]);

        return response()->json(['data' => $mouvement->load('article')], 201);
    }
}
