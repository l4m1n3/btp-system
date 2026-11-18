<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChantierResource;
use App\Models\Chantier;
use App\Models\Tache;
use Illuminate\Http\Request;

class ChantierController extends Controller
{
    /** GET /api/chantiers — chantiers visibles (actifs par défaut, ?tous=1 pour tout). */
    public function index(Request $request)
    {
        $this->authorize('chantiers.view');

        return ChantierResource::collection(
            Chantier::visiblePour($request->user())
                ->when(! $request->boolean('tous'), fn ($q) => $q->actifs())
                ->with(['marche', 'chef', 'taches'])->orderBy('nom')->get()
        );
    }

    public function show(Chantier $chantier)
    {
        $this->authorize('chantier.voir', $chantier);

        return new ChantierResource($chantier->load(['marche', 'chef', 'taches']));
    }

    /** PATCH /api/chantiers/{chantier}/taches/{tache} — mise à jour de l'avancement. */
    public function avancement(Request $request, Chantier $chantier, Tache $tache)
    {
        $this->authorize('chantier.suivre', $chantier);
        abort_unless($tache->chantier_id === $chantier->id, 404);
        $data = $request->validate(['avancement' => 'required|integer|between:0,100']);
        $tache->update($data);

        return new ChantierResource($chantier->fresh()->load(['marche', 'chef', 'taches']));
    }
}
