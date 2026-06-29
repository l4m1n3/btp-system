<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Depense;
use App\Models\Patron;
use App\Models\Chantier;
use App\Models\Planning;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\RapportHebdomadaireMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class RapportController extends Controller
{
        public function generateRapport(Request $request)
        {
            $debut = Carbon::now()->startOfWeek();
            $fin   = Carbon::now()->endOfWeek();

            $today = Carbon::now();
            $depenses = Depense::with('planning')
            ->where('chantier_id', $request->chantier_id)
            ->get();

            $total = $depenses->sum('montant');

            $pdf = Pdf::loadView('pdf.rapport', [
                'depenses' => $depenses,
                'total' => $total,
                'debut' => $debut->format('d/m/Y'),
                'fin' => $fin->format('d/m/Y'),
            ]);

            $filePath = storage_path('app/rapports/rapport_' . $today . '.pdf');
            $pdf->save($filePath);
            return redirect()->back()->with('success', 'Rapport créé avec succès');
            // $patrons = Patron::all();

            // foreach ($patrons as $patron) {
            //     // 📧 Email
            //     Mail::to($patron->email)
            //         ->send(new RapportHebdomadaireMail($filePath));

            //     // 📲 WhatsApp
            //     // $this->sendWhatsApp($patron->telephone, $filePath);
            // }
        }

    private function sendWhatsApp(string $numero, string $filePath)
    {
        // $client = new Client(
        //     config('services.twilio.sid'),
        //     config('services.twilio.token')
        // );

        // $client->messages->create(
        //     "whatsapp:$numero",
        //     [
        //         'from' => 'whatsapp:+14155238886',
        //         'body' => 'Voici le rapport hebdomadaire des dépenses',
        //         'mediaUrl' => [asset('storage/rapport_hebdomadaire.pdf')]
        //     ]
        // );
    }
    public function details($id)
    {
        $company = Patron::find($id);
        $chantiers = Chantier::where('entreprise_id', $id)->paginate(5);
        $depenses = Depense::whereIn('chantier_id', $chantiers->pluck('id'))->paginate(10);
        // dd($depenses);
        return view('rapport.rapport', compact('company', 'chantiers', 'depenses'));
    }

    public function depenseEntreprise($id)
    {

        // $chantiers = Chantier::where('entreprise_id', $id)->first(); // un seul objet
        // dd($chantiers);
        $planning = Planning::where('chantier_id', $id)->get();
        $depenses = Depense::where('chantier_id', $id)->paginate(10);

        // dd($depenses);
        return view('users.depense', compact('depenses', 'planning'));
    }

    //     public function depenseListe(Request $request)
    // {
    //     $email = auth()->user()->email;
    //     $patron = Patron::where('email', $email)->first();
    //     // dd($patron);
    //     if (!$patron) {
    //         return redirect()->back()->withErrors('Patron non trouvé.');
    //     }
    //     // Récupérer les filtres depuis la requête
    //     $chantierId = $request->input('chantier_id');
    //     $dateDebut = $request->input('date_debut');
    //     $dateFin = $request->input('date_fin');

    //     // Base query
    //     $query = Depense::query();

    //     // Filtrer par chantier si sélectionné
    //     if ($chantierId) {
    //         $query->where('chantier_id', $chantierId);
    //     }

    //     // Filtrer par dates si fournies
    //     if ($dateDebut) {
    //         $query->whereDate('date_depense', '>=', $dateDebut);
    //     }
    //     if ($dateFin) {
    //         $query->whereDate('date_depense', '<=', $dateFin);
    //     }

    //     // Pagination
    //     $depenses = $query->orderBy('date_depense', 'desc')->paginate(10);

    //     // Récupérer tous les chantiers pour le dropdown du filtre
    //     $chantiers = Chantier::orderBy('nom')->get();

    //     return view('users.listeDep', compact('depenses', 'chantiers'));
    // }
    public function depenseListe(Request $request)
    {
        $email = auth()->user()->email;
        $patron = Patron::where('email', $email)->first();

        if (!$patron) {
            return redirect()->back()->withErrors('Patron non trouvé.');
        }

        // Récupérer les filtres depuis la requête
        $chantierId = $request->input('chantier_id');
        $dateDebut = $request->input('date_debut');
        $dateFin = $request->input('date_fin');
        // Récupérer les chantiers du patron
        $chantiersPatron = $patron->chantiers()->pluck('id')->toArray();
        // Base query : uniquement les dépenses liées aux chantiers du patron
        $query = Depense::whereIn('chantier_id', $chantiersPatron);
        // Filtrer par chantier si sélectionné et si appartient au patron
        if ($chantierId && in_array($chantierId, $chantiersPatron)) {
            $query->where('chantier_id', $chantierId);
        }
        // Filtrer par dates si fournies
        if ($dateDebut) {
            $query->whereDate('date_depense', '>=', $dateDebut);
        }
        if ($dateFin) {
            $query->whereDate('date_depense', '<=', $dateFin);
        }
        // Pagination
        $depenses = $query->orderBy('date_depense', 'desc')->paginate(10);
        // Récupérer tous les chantiers du patron pour le dropdown du filtre
        $chantiers = $patron->chantiers()->orderBy('nom')->get();

        return view('users.listeDep', compact('depenses', 'chantiers'));
    }

    public function storeDepense(Request $request)
    {
        try {
            $request->validate([
                'planning_id' => 'required|exists:plannings,id',
                'chantier_id' => 'required|exists:chantiers,id',
                'categorie' => 'required|string|max:255',
                'description' => 'required|string',
                'montant' => 'required|numeric|min:0',
                'responsable' => 'nullable|string|max:255',
                'date_depense' => 'nullable|date',
                'justificatif' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            DB::transaction(function () use ($request) {

                $data = $request->all();
                // dd($data);
                // Upload fichier
                if ($request->hasFile('justificatif')) {
                    $file = $request->file('justificatif');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('justificatifs', $filename, 'public');

                    $data['justificatif'] = $path;
                }
                // Création dépense
                $depense = Depense::create($data);
                // Récupération planning
                $planning = Planning::findOrFail($request->planning_id);
                // Recalcul du total des dépenses
                $totalDepenses = Depense::where('planning_id', $planning->id)->sum('montant');
                // dd($totalDepenses);
                // Mise à jour du cout réel
                $planning->update([
                    'cout_reel' => $totalDepenses,
                ]);

                return $depense;
            });

            return redirect()->back()->with('success', 'depense créé avec succès');
        } catch (\Throwable $th) {
            Log::error('Depense Creation Error', [
                'error' => $th->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Erreur lors de l\'enregistrement de la dépense');
        }
    }
}
