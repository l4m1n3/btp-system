<?php

namespace App\Http\Controllers;

use App\Exports\DepensesExport;
use App\Models\Bulletin;
use App\Models\Chantier;
use App\Models\Depense;
use App\Models\PeriodePaie;
use App\Support\FiltreDepenses;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Documents générés : PDF (bulletins, journal de paie, situation de chantier, état des dépenses),
 * export Excel et justificatifs de dépenses (stockage privé).
 */
class DocumentController extends Controller
{
    public function bulletin(Bulletin $bulletin)
    {
        $this->authorize('paie.view');
        $bulletin->load(['employe', 'periode', 'lignes', 'avances', 'chantier']);

        return Pdf::loadView('pdf.bulletin', ['b' => $bulletin])
            ->stream('bulletin-'.$bulletin->periode->mois.'-'.Str::slug($bulletin->employe->nom_complet).'.pdf');
    }

    public function journalPaie(PeriodePaie $periode)
    {
        $this->authorize('paie.view');
        $bulletins = $periode->bulletins()->with(['employe', 'chantier'])->get()->sortBy(fn ($b) => $b->employe->nom);

        return Pdf::loadView('pdf.journal-paie', compact('periode', 'bulletins'))->setPaper('a4', 'landscape')
            ->stream('journal-paie-'.$periode->mois.'.pdf');
    }

    public function chantier(Chantier $chantier)
    {
        $this->authorize('chantier.voir', $chantier);
        $chantier->load(['marche.client', 'chef', 'taches']);
        $depenses = $chantier->depenses()->validees()->with('categorie')->orderBy('date_depense')->get();
        $materiaux = $chantier->mouvementsStock()->with('article')->where('type', 'sortie')->orderBy('date_mouvement')->get();

        return Pdf::loadView('pdf.chantier', compact('chantier', 'depenses', 'materiaux'))
            ->stream('situation-'.Str::slug($chantier->code).'-'.now()->format('Y-m-d').'.pdf');
    }

    public function depenses(Request $request)
    {
        $this->authorize('rapports.view');
        $depenses = FiltreDepenses::requete($request, $request->user())->get();

        return Pdf::loadView('pdf.depenses', ['depenses' => $depenses, 'filtre' => FiltreDepenses::description($request)])
            ->setPaper('a4', 'landscape')->stream('etat-depenses-'.now()->format('Y-m-d').'.pdf');
    }

    public function exportDepenses(Request $request)
    {
        $this->authorize('rapports.view');

        return Excel::download(new DepensesExport(FiltreDepenses::requete($request, $request->user())), 'depenses-'.now()->format('Y-m-d').'.xlsx');
    }

    public function justificatif(Depense $depense)
    {
        abort_unless($depense->justificatif && Depense::visiblePour(request()->user())->whereKey($depense->id)->exists(), 404);
        abort_unless(Gate::any(['depenses.view', 'depenses.create']), 403);

        return Storage::disk('local')->response($depense->justificatif);
    }
}
