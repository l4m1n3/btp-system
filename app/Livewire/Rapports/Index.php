<?php

namespace App\Livewire\Rapports;

use App\Enums\PorteeDepense;
use App\Models\Chantier;
use App\Models\Decompte;
use App\Models\Depense;
use App\Models\MouvementStock;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Rapports')]
class Index extends Component
{
    #[Url]
    public string $du = '';

    #[Url]
    public string $au = '';

    public function mount(): void
    {
        $this->authorize('rapports.view');
        $this->du = $this->du ?: now()->startOfYear()->toDateString();
        $this->au = $this->au ?: now()->toDateString();
    }

    public function periode(string $p): void
    {
        [$this->du, $this->au] = match ($p) {
            'mois' => [now()->startOfMonth()->toDateString(), now()->toDateString()],
            'mois_prec' => [now()->subMonthNoOverflow()->startOfMonth()->toDateString(), now()->subMonthNoOverflow()->endOfMonth()->toDateString()],
            'trimestre' => [now()->startOfQuarter()->toDateString(), now()->toDateString()],
            default => [now()->startOfYear()->toDateString(), now()->toDateString()],
        };
    }

    public function render()
    {
        $this->validate(['du' => 'required|date', 'au' => 'required|date|after_or_equal:du']);
        $depenses = fn () => Depense::validees()->whereBetween('date_depense', [$this->du, $this->au]);

        $parCategorie = $depenses()->join('categories_depenses', 'categories_depenses.id', '=', 'depenses.categorie_id')
            ->selectRaw('depenses.portee, categories_depenses.nom as categorie, SUM(depenses.montant) as total')
            ->groupBy('depenses.portee', 'categories_depenses.nom')->orderByDesc('total')->get()
            ->groupBy(fn ($r) => $r->portee->value);

        $depChantier = $depenses()->whereNotNull('chantier_id')->selectRaw('chantier_id, SUM(montant) as total')->groupBy('chantier_id')->pluck('total', 'chantier_id');
        $matChantier = MouvementStock::where('type', 'sortie')->whereBetween('date_mouvement', [$this->du, $this->au])
            ->selectRaw('chantier_id, SUM(quantite * prix_unitaire) as total')->groupBy('chantier_id')->pluck('total', 'chantier_id');

        $chantiers = Chantier::with(['taches', 'marche'])->whereIn('id', $depChantier->keys()->merge($matChantier->keys()))
            ->orWhereIn('statut', ['en_cours', 'en_preparation', 'suspendu'])->orderBy('nom')->get()
            ->map(fn ($c) => [
                'model' => $c,
                'depenses' => (float) ($depChantier[$c->id] ?? 0),
                'materiaux' => (float) ($matChantier[$c->id] ?? 0),
                'cumul' => $c->coutTotal(),
                'conso' => $c->tauxConsommationBudget(),
                'avancement' => $c->avancement,
            ]);

        $totalInterne = (float) $depenses()->where('portee', PorteeDepense::Interne)->sum('montant');
        $totalChantiers = (float) $depenses()->where('portee', PorteeDepense::Chantier)->sum('montant');
        $encaisse = (float) Decompte::whereBetween('date_paiement', [$this->du, $this->au])->sum('montant_paye');
        $facture = (float) Decompte::where('statut', '!=', 'rejete')->whereBetween('date_decompte', [$this->du, $this->au])->sum('montant_ht');

        return view('livewire.rapports.index', compact('parCategorie', 'chantiers', 'totalInterne', 'totalChantiers', 'encaisse', 'facture') + [
            'parametres' => http_build_query(['du' => $this->du, 'au' => $this->au, 'statut' => 'validee']),
        ]);
    }
}
