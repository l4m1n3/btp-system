<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chantier;
use App\Models\RapportJournalier;
use App\Services\JournalService;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index(Chantier $chantier)
    {
        $this->authorize('chantier.voir', $chantier);

        return response()->json(['data' => $chantier->rapports()->with('lignes')->limit(30)->get()]);
    }

    /** POST /api/chantiers/{chantier}/journal — crée ou remplace le rapport du jour indiqué. */
    public function store(Request $request, Chantier $chantier, JournalService $journal)
    {
        $this->authorize('chantier.suivre', $chantier);
        $existant = RapportJournalier::where('chantier_id', $chantier->id)->whereDate('date_rapport', $request->input('date_rapport'))->first();
        $data = $request->validate(JournalService::regles($chantier, $existant));
        $rapport = $journal->enregistrer($chantier, $data, $request->user(), $existant);

        return response()->json(['data' => $rapport], $existant ? 200 : 201);
    }
}
