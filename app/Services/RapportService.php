<?php

namespace App\Services;

use App\Models\Depense;
use App\Models\Patron;
use App\Mail\RapportHebdomadaireMail; 
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RapportService
{
    /**
     * Générer et envoyer le rapport hebdomadaire
     */
    public function envoyerRapportHebdomadaire(): void
    {
        // 📅 Période : semaine en cours
        $start = Carbon::now()->startOfWeek();
        $end   = Carbon::now()->endOfWeek();

        // 💰 Dépenses de la semaine
        $depenses = Depense::whereBetween('date_depense', [$start, $end])
            ->with('chantier')
            ->get();

        if ($depenses->isEmpty()) {
            return;
        }

        // 👔 Patron
        $patron = Patron::first(); // ou ->where('actif', true)->first()

        if (!$patron) {
            return;
        }

        // 📄 Génération PDF
        $pdf = Pdf::loadView('pdf.rapport_hebdomadaire', [
            'depenses' => $depenses,
            'start' => $start,
            'end' => $end,
            'patron' => $patron,
        ]);

        $fileName = 'rapport_depenses_' . now()->format('Y_m_d') . '.pdf';
        $filePath = storage_path('app/rapports/' . $fileName);

        if (!file_exists(storage_path('app/rapports'))) {
            mkdir(storage_path('app/rapports'), 0755, true);
        }

        file_put_contents($filePath, $pdf->output());

        // 📧 Envoi par email
        if ($patron->email) {
            Mail::to($patron->email)
                ->send(new RapportHebdomadaireMail($filePath));
        }

        // 📱 Envoi WhatsApp
        // if ($patron->telephone) {
        //     $this->envoyerWhatsapp($patron->telephone, $filePath);
        // }
    }

    /**
     * Envoi WhatsApp (à adapter selon l'API utilisée)
     */
    // private function envoyerWhatsapp(string $numero, string $filePath): void
    // {
    //     $token = config('services.whatsapp.token');
    //     $phone_number_id = config('services.whatsapp.phone_number_id');

    //     // Vérifier que le fichier existe
    //     if (!file_exists($filePath)) {
    //         Log::error("Fichier PDF introuvable: $filePath");
    //         return;
    //     }

    //     // 📤 Upload du fichier PDF sur WhatsApp
    //     $fileData = base64_encode(file_get_contents($filePath));
    //     $fileName = basename($filePath);

    //     $mediaResponse = Http::withToken($token)
    //         ->post("https://graph.facebook.com/v17.0/$phone_number_id/media", [
    //             'messaging_product' => 'whatsapp',
    //             'file' => $fileData,
    //             'type' => 'application/pdf',
    //             'filename' => $fileName,
    //         ]);

    //     if (!$mediaResponse->successful()) {
    //         Log::error('Erreur upload PDF WhatsApp: '.$mediaResponse->body());
    //         return;
    //     }

    //     $mediaId = $mediaResponse->json()['id'];

    //     // 📩 Envoi du message avec le PDF
    //     $messageResponse = Http::withToken($token)
    //         ->post("https://graph.facebook.com/v17.0/$phone_number_id/messages", [
    //             'messaging_product' => 'whatsapp',
    //             'to' => $numero,
    //             'type' => 'document',
    //             'document' => [
    //                 'id' => $mediaId,
    //                 'caption' => 'Voici votre rapport hebdomadaire',
    //             ],
    //         ]);

    //     if (!$messageResponse->successful()) {
    //         Log::error('Erreur envoi WhatsApp: '.$messageResponse->body());
    //     }
    // }
}
