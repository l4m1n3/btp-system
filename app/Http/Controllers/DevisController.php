<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\DevisImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Devis;
use App\Models\Chantier;

class DevisController extends Controller
{
    public function show($chantierId)
    {
        $chantier = Chantier::findOrFail($chantierId);
        // $devis = Devis::where('chantier_id', $chantierId)->get();
        $devis = Devis::where('chantier_id', $chantierId)
            ->latest()
            ->first();
        $devisList = Devis::where('chantier_id', $chantierId)
            ->get();
        $devisActif = $devis;
        // dd($devisActif);

        // Calcul des statistiques
        // $totalMontant = $devis->sum('amount');
        // $devisValides = $devis->where('date_limite', '>', now())->count();
        // $devisAExpirer = $devis->where('date_limite', '>', now())
        //     ->where('date_limite', '<=', now()->addDays(7))
        //     ->count();
        // $devisExpires = $devis->where('date_limite', '<', now())->count();
        // $montantMoyen = $devis->count() > 0 ? $totalMontant / $devis->count() : 0;

        return view('users.devisForm', compact(
            'chantier',
            'devis',
            'devisList',   // Collection
            'devisActif',  // Devis|null
            // 'totalMontant',
            // 'devisValides',
            // 'devisAExpirer',
            // 'devisExpires',
            // 'montantMoyen'
        ));
    }

    public function import(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:xls,xlsx',
            'devis_id' => 'required|exists:devis,id',
        ]);
        $devis = Devis::find($request->input('devis_id'));

        Excel::import(new DevisImport($devis), $request->file('fichier'));

        return redirect()->back()->with('success', 'Devis importé avec succès');
    }

    public function store(Request $request)
    {
        $request->validate([
            'reference' => 'nullable|string|max:255',
            'intitule' => 'nullable|string|max:255',
            'montant' => 'nullable|numeric',
            'date_emission' => 'nullable|date',
            'chantier_id' => 'required|exists:chantiers,id',
        ]);

        Devis::create([
            'reference' => $request->input('reference'),
            'intitule' => $request->input('intitule'),
            'montant' => $request->input('montant', 0),
            'date_emission' => $request->input('date_emission'),
            'chantier_id' => $request->input('chantier_id'),
        ]);

        return redirect()->back()
            ->with('success', 'Devis créé avec succès.');
    }
}
