<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Depense;
use App\Models\Patron;
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
}
