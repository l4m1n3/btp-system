<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chantier;
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
}
