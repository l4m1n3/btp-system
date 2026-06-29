<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chantier;
use App\Models\Planning;
use App\Models\FicheJournaliere;
use App\Models\Depense;
use Illuminate\Support\Facades\Log;

class ChantierController extends Controller
{
    public function index()
    {
        $chantiers = Chantier::all();
        return response()->json($chantiers);
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nom' => 'required|string|max:255',
                'description' => 'nullable|string',
                'statut' => 'nullable|string|max:100',
                'adresse' => 'nullable|string|max:255',
                'budget_total' => 'required|numeric|min:0',
                'entreprise_id' => 'required',
            ]);
            // dd( $request->all());
            $chantier = Chantier::create($request->all());
            // if ($request->expectsJson()) {
            //     // Générer un token (ex: Laravel Sanctum)
            //     // $token = $chantier->createToken('api-token')->plainTextToken;

            //     return response()->json([
            //         // 'access_token' => $token,
            //         'token_type' => 'Bearer',
            //         'chantier' => $chantier
            //     ], 201);
            // }
            // --- Sinon c'est une requête web
            return redirect()->back()->with('success', 'Chantier créé avec succès');
        } catch (\Throwable $th) {
            Log::error('Chantier Creation Error', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
        }
    }
    public function show($id)
    {
        $chantiers = Chantier::find($id);
        return response()->json($chantiers);
    }
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nom' => 'sometimes|required|string|max:255',
                'budget_total' => 'sometimes|required|numeric|min:0',
                'patron_id' => 'sometimes|required|exists:patrons,id',
            ]);

            $chantier = Chantier::find($id);
            if (!$chantier) {
                return response()->json(['message' => 'Chantier not found'], 404);
            }

            $chantier->update($request->all());
            return response()->json($chantier);
        } catch (\Throwable $th) {
            Log::error('Chantier Update Error', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
        }
    }
    public function destroy($id)
    {

        try {
            $chantier = Chantier::find($id);
            if (!$chantier) {
                return response()->json(['message' => 'Chantier not found'], 404);
            }

            $chantier->delete();
            return response()->json(['message' => 'Chantier deleted successfully']);
        } catch (\Throwable $th) {
            Log::error('Chantier Deletion Error', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
        }
    }

    public function planning($id)
    {
        $chantier = Chantier::findOrFail($id);
        $plannings = $chantier->plannings()->orderBy('date_debut_prevue')->get();
        return view('users.planning', compact('chantier', 'plannings'));
    }
    public function storePlanning(Request $request)
    {
        try {
            $request->validate([
                'chantier_id' => 'required|exists:chantiers,id',
                'activite' => 'required|string|max:255',
                'responsable' => 'nullable|string|max:255',
                'note' => 'nullable|string',
                'date_debut_prevue' => 'required|date',
                'date_fin_prevue' => 'required|date|after_or_equal:date_debut_prevue',
                'cout_prevu' => 'required|numeric|min:0',
            ]);

            $chantier = Chantier::findOrFail($request->input('chantier_id'));

            $planning = $chantier->plannings()->create($request->all());

            return redirect()->back()->with('success', 'Tâche ajoutée au planning avec succès');
        } catch (\Throwable $th) {
            Log::error('Planning Creation Error', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'ajout de la tâche au planning');
        }
    }
    public function editPlanning($id)
    {
        $planning = Planning::findOrFail($id);
        return view('users.edit_planning', compact('planning'));
    }
    public function updatePlanning(Request $request, $id)
    {
        $request->validate([
            'activite' => 'required|string|max:255',
            'responsable' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'date_debut_prevue' => 'required|date',
            'date_fin_prevue' => 'required|date|after_or_equal:date_debut_prevue',
            'cout_prevu' => 'required|numeric|min:0',
            'statut' => 'required|in:planifie,en_cours,termine,retarde,annule',
        ]);

        $planning = Planning::findOrFail($id);
        $planning->update($request->all());

        return redirect()->back()->with('success', 'Tâche mise à jour avec succès');
    }
    public function deletePlanning($id)
    {
        $planning = Planning::findOrFail($id);
        $planning->delete();
        return redirect()->back()->with('success', 'Tâche supprimée avec succès');
    }
    public function details($id)
    {
        $chantier = Chantier::with([
            'plannings',
        ])->findOrFail($id);

        $chantier = Chantier::findOrFail($id);
        $fiches = FicheJournaliere::where('chantier_id', $id)
            ->orderBy('date', 'desc')
            ->paginate(10);
        $plannings = $chantier->plannings()->orderBy('date_debut_prevue')->get();
        $planing = Planning::where('chantier_id', $id)->get();
        $depenses = Depense::where('chantier_id', $id)->paginate(10);

        return view('users.chantiers.details', compact('chantier', 'fiches', 'plannings','depenses','planing'));
    }
}
