<?php

namespace App\Http\Controllers\Api;

use App\Enums\ModePaiement;
use App\Http\Controllers\Controller;
use App\Http\Resources\DepenseResource;
use App\Models\CategorieDepense;
use App\Models\Depense;
use App\Models\Fournisseur;
use App\Services\DepenseService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepenseController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('depenses.view');

        return DepenseResource::collection(
            Depense::visiblePour($request->user())->with(['chantier', 'categorie'])
                ->when($request->query('chantier_id'), fn ($q, $v) => $q->where('chantier_id', $v))
                ->when($request->query('statut'), fn ($q, $v) => $q->where('statut', $v))
                ->latest('date_depense')->latest('id')->paginate(30)
        );
    }

    /** POST /api/depenses (multipart si justificatif). */
    public function store(Request $request, DepenseService $service)
    {
        $this->authorize('depenses.create');
        $data = $request->validate([
            ...DepenseService::regles(),
            'mode_paiement' => ['required', Rule::enum(ModePaiement::class)],
            'justificatif' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
        $depense = $service->enregistrer($data, $request->user(), $request->file('justificatif'));

        return (new DepenseResource($depense->load(['chantier', 'categorie'])))->response()->setStatusCode(201);
    }

    /** GET /api/references — listes utiles aux formulaires mobiles. */
    public function references(Request $request)
    {
        return response()->json([
            'categories' => CategorieDepense::where('systeme', false)->orderBy('nom')->get(['id', 'nom', 'portee']),
            'fournisseurs' => Fournisseur::orderBy('nom')->get(['id', 'nom']),
            'modes_paiement' => ModePaiement::options(),
        ]);
    }
}
