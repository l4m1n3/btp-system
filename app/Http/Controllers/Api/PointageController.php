<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chantier;
use App\Models\Pointage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PointageController extends Controller
{
    /** GET /api/chantiers/{chantier}/pointage?date=AAAA-MM-JJ */
    public function index(Request $request, Chantier $chantier)
    {
        $this->autoriser($chantier);
        $date = $request->query('date', now()->toDateString());
        $employes = $chantier->employes()->actifs()->orderBy('nom')->get();
        $pointages = Pointage::whereIn('employe_id', $employes->pluck('id'))->whereDate('date_pointage', $date)->get()->keyBy('employe_id');

        return response()->json(['date' => $date, 'data' => $employes->map(fn ($e) => [
            'employe_id' => $e->id, 'matricule' => $e->matricule, 'nom' => $e->nom_complet, 'fonction' => $e->fonction,
            'present' => $pointages[$e->id]->present ?? null, 'heures_sup' => (float) ($pointages[$e->id]->heures_sup ?? 0),
            'observation' => $pointages[$e->id]->observation ?? null,
        ])]);
    }

    /** POST /api/chantiers/{chantier}/pointage */
    public function store(Request $request, Chantier $chantier)
    {
        $this->autoriser($chantier);
        $ids = $chantier->employes()->actifs()->pluck('id')->all();
        $data = $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'pointages' => 'required|array|min:1',
            'pointages.*.employe_id' => ['required', 'integer', Rule::in($ids)],
            'pointages.*.present' => 'required|boolean',
            'pointages.*.heures_sup' => 'nullable|numeric|between:0,12',
            'pointages.*.observation' => 'nullable|string|max:255',
        ], ['pointages.*.employe_id.in' => "Cet employé n'est pas affecté à ce chantier."]);

        DB::transaction(function () use ($data, $chantier, $request) {
            foreach ($data['pointages'] as $p) {
                Pointage::pour((int) $p['employe_id'], $data['date'])->fill(
                    ['chantier_id' => $chantier->id, 'present' => $p['present'], 'heures_sup' => $p['present'] ? ($p['heures_sup'] ?? 0) : 0,
                        'observation' => $p['observation'] ?? null, 'saisi_par' => $request->user()->id],
                )->save();
            }
        });

        return response()->json(['message' => count($data['pointages']).' pointage(s) enregistré(s).']);
    }

    private function autoriser(Chantier $chantier): void
    {
        $this->authorize('pointage.saisir');
        $this->authorize('chantier.voir', $chantier);
    }
}
