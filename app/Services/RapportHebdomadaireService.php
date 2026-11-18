<?php

namespace App\Services;

use App\Enums\StatutDepense;
use App\Mail\RapportHebdomadaireMail;
use App\Models\Chantier;
use App\Models\Depense;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Rapport de la semaine écoulée (dépenses validées + avancement des chantiers)
 * envoyé par e-mail aux rôles configurés dans config/btp.php.
 */
class RapportHebdomadaireService
{
    public function donnees(Carbon $debut, Carbon $fin): array
    {
        $depenses = Depense::with(['chantier', 'categorie'])
            ->where('statut', StatutDepense::Validee)
            ->whereBetween('date_depense', [$debut->toDateString(), $fin->toDateString()])
            ->orderBy('date_depense')->get();

        $chantiers = Chantier::actifs()->with('taches')->orderBy('nom')->get()->map(fn ($c) => [
            'model' => $c, 'avancement' => $c->avancement, 'cout' => $c->coutTotal(), 'conso' => $c->tauxConsommationBudget(),
        ]);

        return compact('depenses', 'chantiers', 'debut', 'fin');
    }

    public function envoyer(?Carbon $reference = null): int
    {
        $reference ??= now()->subWeek();
        $debut = $reference->copy()->startOfWeek();
        $fin = $reference->copy()->endOfWeek();

        $destinataires = User::whereIn('role', config('btp.rapport_hebdomadaire_roles'))->where('actif', true)->pluck('email');
        if ($destinataires->isEmpty()) {
            Log::info('Rapport hebdomadaire : aucun destinataire.');

            return 0;
        }

        $pdf = Pdf::loadView('pdf.rapport-hebdomadaire', $this->donnees($debut, $fin))->output();
        Mail::to($destinataires->all())->send(new RapportHebdomadaireMail($pdf, $debut, $fin));
        Log::info('Rapport hebdomadaire envoyé', ['destinataires' => $destinataires->count()]);

        return $destinataires->count();
    }
}
