<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use App\Models\Planning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DepenseController extends Controller
{
    public function index()
    {
        $depenses = Depense::all();
        return response()->json($depenses);
    }
    // public function store(Request $request)
    // {
    //     try {
    //         // Validation 
    //         $request->validate([
    //             'chantier_id' => 'required|exists:chantiers,id',
    //             'categorie' => 'required|string|max:255',
    //             'description' => 'required|string',
    //             'montant' => 'required|numeric|min:0',
    //             'responsable' => 'nullable|string|max:255',
    //             'date_depense' => 'nullable|date',
    //             'justificatif' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
    //         ]);

    //         $data = $request->all();
    //         if ($request->hasFile('justificatif')) {
    //             $file = $request->file('justificatif');
    //             $filename = time().'_'.$file->getClientOriginalName();
    //             $path = $file->storeAs('justificatifs', $filename, 'public');

    //             $data['justificatif'] = $path; // recommandé
    //         }
    //         $montant = $request->input('montant');
    //         // $request->file('photo')->store('photos/emp', 'public') ;
    //         $depense = Depense::create($data);
    //         if($depense) {
    //             $updatePlanning=Planning::where('chantier_id', $depense->chantier_id)
    //                 ->where('activite', 'Dépenses')
    //                 ->first();
    //             if ($updatePlanning) {
    //                 $updatePlanning->cout_reel += $montant;
    //                 $updatePlanning->save();
    //             }
    //             Log::info('Dépense créée avec succès', [
    //                 'depense_id' => $depense->id,
    //                 'chantier_id' => $depense->chantier_id,
    //                 'montant' => $depense->montant,
    //             ]);
    //         }

    //         return response()->json($depense, 201);
    //     } catch (\Throwable $th) {
    //         Log::error('Depense Creation Error', [
    //             'error' => $th->getMessage(),
    //             'trace' => $th->getTraceAsString(),
    //         ]);
    //         return response()->json([
    //             'message' => 'Erreur lors de l\'enregistrement de la dépense'
    //         ], 500);
    //     }
    // }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'chantier_id' => 'required|exists:chantiers,id',
                'categorie' => 'required|string|max:255',
                'description' => 'required|string',
                'montant' => 'required|numeric|min:0',
                'responsable' => 'nullable|string|max:255',
                'date_depense' => 'nullable|date',
                'justificatif' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            $data = $request->only([
                'chantier_id',
                'categorie',
                'description',
                'montant',
                'responsable',
                'date_depense',
            ]);

            // Upload justificatif
            if ($request->hasFile('justificatif')) {

                $file = $request->file('justificatif');

                $filename = time() . '_' .
                    Str::slug(
                        pathinfo(
                            $file->getClientOriginalName(),
                            PATHINFO_FILENAME
                        )
                    ) . '.' . $file->getClientOriginalExtension();

                $path = $file->storeAs(
                    'justificatifs',
                    $filename,
                    'public'
                );

                $data['justificatif'] = $path;
            }
            // Création dépense
            $depense = Depense::create($data);

            // Mise à jour planning
            $planning = Planning::where('chantier_id', $depense->chantier_id)
                ->where('activite', 'Dépenses')
                ->first();
                
            if ($planning) {

                $planning->cout_reel =
                    ($planning->cout_reel ?? 0)
                    + $depense->montant;

                $planning->save();
            }

            Log::info('Dépense créée avec succès', [
                'depense_id' => $depense->id,
                'chantier_id' => $depense->chantier_id,
                'montant' => $depense->montant,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dépense enregistrée avec succès',
                'data' => $depense
            ], 201);
        } catch (\Throwable $th) {

            DB::rollBack();

            Log::error('Depense Creation Error', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement de la dépense',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function depenseEntreprise(Request $request, $chantierId)
    {
        $depenses = Depense::where('chantier_id', $chantierId)->get();
        return response()->json($depenses);
    }
}
