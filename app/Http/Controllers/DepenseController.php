<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DepenseController extends Controller
{
    public function index()
    {
        $depenses = Depense::all();
        return response()->json($depenses);
    }
    public function store(Request $request)
    {
        try {
            // Validation
            $request->validate([
                'chantier_id' => 'required|exists:chantiers,id',
                'categorie' => 'required|string|max:255',
                'description' => 'required|string',
                'montant' => 'required|numeric|min:0',
                'responsable' => 'nullable|string|max:255',
                'date_depense' => 'nullable|date',
                'justificatif' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            $data = $request->all();

            // Gestion du fichier justificatif
            if ($request->hasFile('justificatif')) {
                $file = $request->file('justificatif');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('public/justificatifs', $filename);
                $data['justificatif'] = $filename; // Stocke seulement le nom du fichier
            }

            $depense = Depense::create($data);

            return response()->json($depense, 201);
        } catch (\Throwable $th) {
            Log::error('Depense Creation Error', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
            return response()->json([
                'message' => 'Erreur lors de l\'enregistrement de la dépense'
            ], 500);
        }
    }
}
