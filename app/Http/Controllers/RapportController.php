<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Depense;
use App\Models\Patron;
use App\Models\Chantier;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\RapportHebdomadaireMail;

class RapportController extends Controller
{
    public function generateHebdomadaire()
    {
        $debut = Carbon::now()->startOfWeek();
        $fin   = Carbon::now()->endOfWeek();

        $depenses = Depense::whereBetween('date_depense', [$debut, $fin])->get();
        $total = $depenses->sum('montant');

        $pdf = Pdf::loadView('rapports.hebdomadaire', [
            'depenses' => $depenses,
            'total' => $total,
            'debut' => $debut->format('d/m/Y'),
            'fin' => $fin->format('d/m/Y'),
        ]);

        $filePath = storage_path('app/rapport_hebdomadaire.pdf');
        $pdf->save($filePath);

        $patrons = Patron::all();

        foreach ($patrons as $patron) {
            // 📧 Email
            Mail::to($patron->email)
                ->send(new RapportHebdomadaireMail($filePath));

            // 📲 WhatsApp
            // $this->sendWhatsApp($patron->telephone, $filePath);
        }
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

        // $chantier = Chantier::where('entreprise_id', $id)->first(); // un seul objet
        // dd($chantier);
        $depenses = Depense::where('chantier_id', $id)->paginate(10);
        // dd($depenses);
        return view('users.depense', compact('depenses'));
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

}
