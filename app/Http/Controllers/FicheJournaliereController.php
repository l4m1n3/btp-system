<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FicheJournaliere;
use App\Models\Chantier;

class FicheJournaliereController extends Controller
{ 
    public function index($chantier_id)
    {
        $chantier = Chantier::findOrFail($chantier_id);
        $fiches = FicheJournaliere::where('chantier_id', $chantier_id)
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('users.fiches.index', compact('chantier', 'fiches'));
    }

    public function create($chantier_id)
    {
        $chantier = Chantier::findOrFail($chantier_id);
        return view('users.fiches.create', compact('chantier'));
    }

    public function store(Request $request, $chantier_id)
    {
        $request->validate([
            'date'          => 'required|date',
            'observations'  => 'nullable|string',
            'evenements'    => 'nullable|string',
        ]);

        $fiche = FicheJournaliere::create([
            'chantier_id'   => $chantier_id,
            'date'          => $request->date,
            'observations'  => $request->observations,
            'evenements'    => $request->evenements,
        ]);

        // Personnel (section I)
        if ($request->has('personnel')) {
            foreach ($request->personnel as $p) {
                if (!empty($p['designation'])) {
                    $fiche->personnel()->create([
                        'designation' => $p['designation'],
                        'hommes'      => $p['hommes'] ?? 0,
                        'femmes'      => $p['femmes'] ?? 0,
                    ]);
                }
            }
        }

        // Matériel (section II)
        if ($request->has('materiel')) {
            foreach ($request->materiel as $m) {
                if (!empty($m['designation'])) {
                    $fiche->materiel()->create([
                        'designation' => $m['designation'],
                        'nombre'      => $m['nombre'] ?? 1,
                        'etat'        => $m['etat'] ?? null,
                    ]);
                }
            }
        }

        // Approvisionnements (section III)
        if ($request->has('appros')) {
            foreach ($request->appros as $a) {
                if (!empty($a['designation'])) {
                    $fiche->appros()->create([
                        'designation' => $a['designation'],
                        'unite'       => $a['unite'] ?? null,
                        'quantite'    => $a['quantite'] ?? null,
                    ]);
                }
            }
        }

        // Travaux réalisés (section IV)
        if ($request->has('travaux')) {
            foreach ($request->travaux as $t) {
                if (!empty($t['designation'])) {
                    $fiche->travaux()->create([
                        'designation'       => $t['designation'],
                        'niveau_avancement' => $t['niveau_avancement'] ?? null,
                        'planning_id'       => $t['planning_id'] ?? null,
                    ]);
                }
            }
        }

        return redirect()
            ->back()
            ->with('success', 'Fiche journalière créée avec succès.');
    }

    public function show($chantier_id, $id)
    {
        $chantier = Chantier::findOrFail($chantier_id);
        $fiche = FicheJournaliere::with([
                'personnel',
                'materiel',
                'appros',
                'travaux'
            ])->findOrFail($id);

        return view('users.fiches.show', compact('chantier', 'fiche'));
    }

    public function edit($chantier_id, $id)
    {
        $chantier = Chantier::findOrFail($chantier_id);
        $fiche = FicheJournaliere::with([
                'personnel',
                'materiel',
                'appros',
                'travaux'
            ])->findOrFail($id);

        return view('users.fiches.edit', compact('chantier', 'fiche'));
    }

    public function update(Request $request, $chantier_id, $id)
    {
        $request->validate([
            'date'         => 'required|date',
            'observations' => 'nullable|string',
            'evenements'   => 'nullable|string',
        ]);

        $fiche = FicheJournaliere::findOrFail($id);
        $fiche->update([
            'date'         => $request->date,
            'observations' => $request->observations,
            'evenements'   => $request->evenements,
        ]);

        // Resync — supprimer et recréer les lignes
        $fiche->personnel()->delete();
        $fiche->materiel()->delete();
        $fiche->appros()->delete();
        $fiche->travaux()->delete();

        if ($request->has('personnel')) {
            foreach ($request->personnel as $p) {
                if (!empty($p['designation'])) {
                    $fiche->personnel()->create($p);
                }
            }
        }
        if ($request->has('materiel')) {
            foreach ($request->materiel as $m) {
                if (!empty($m['designation'])) {
                    $fiche->materiel()->create($m);
                }
            }
        }
        if ($request->has('appros')) {
            foreach ($request->appros as $a) {
                if (!empty($a['designation'])) {
                    $fiche->appros()->create($a);
                }
            }
        }
        if ($request->has('travaux')) {
            foreach ($request->travaux as $t) {
                if (!empty($t['designation'])) {
                    $fiche->travaux()->create($t);
                }
            }
        }

        return redirect()
            ->back()
            ->with('success', 'Fiche mise à jour avec succès.');
    }

    public function destroy($chantier_id, $id)
    {
        $fiche = FicheJournaliere::findOrFail($id);
        $fiche->delete(); // cascade supprime les lignes enfants

        return redirect()
            ->back()
            ->with('success', 'Fiche supprimée.');
    }
}