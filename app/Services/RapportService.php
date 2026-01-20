<?php

namespace App\Services;

use App\Models\Depense;
use App\Models\Patron;
use App\Mail\RapportHebdomadaireMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RapportService
{
    public function envoyerRapportQuotidien(): void
    {
        $this->envoyerRapport(Carbon::today()->startOfDay(), Carbon::today()->endOfDay(), 'quotidien');
    }

    public function envoyerRapportHebdomadaire(): void
    {
        $this->envoyerRapport(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek(), 'hebdomadaire');
    }

    public function envoyerRapportMensuel(): void
    {
        $this->envoyerRapport(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth(), 'mensuel');
    }

    public function envoyerRapportAnnuel(): void
    {
        $this->envoyerRapport(Carbon::now()->startOfYear(), Carbon::now()->endOfYear(), 'annuel');
    }

    /**
     * Méthode générique pour générer PDF et envoyer par email
     */
    private function envoyerRapport(Carbon $start, Carbon $end, string $type): void
    {
        $depenses = Depense::whereBetween('date_depense', [$start, $end])
            ->with('chantier')
            ->get();

        if ($depenses->isEmpty()) {
            Log::info("Aucune dépense pour le rapport $type entre $start et $end.");
            return;
        }

        $patron = Patron::first();
        if (!$patron) {
            Log::error("Pas de patron trouvé pour le rapport $type.");
            return;
        }

        // Génération PDF
        $pdf = Pdf::loadView('pdf.rapport', [
            'depenses' => $depenses,
            'start' => $start,
            'end' => $end,
            'patron' => $patron,
            'type' => $type,
        ]);

        $folder = storage_path('app/rapports');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $fileName = "rapport_{$type}_" . now()->format('Y_m_d_H_i_s') . ".pdf";
        $filePath = $folder . '/' . $fileName;
        file_put_contents($filePath, $pdf->output());
        Log::info("Rapport $type généré : $fileName");

        // Envoi email
        if ($patron->email) {
            try {
                Mail::to($patron->email)->send(new RapportHebdomadaireMail($filePath));
                Log::info("Rapport $type envoyé par email à {$patron->email}");
            } catch (\Exception $e) {
                Log::error("Erreur email rapport $type : " . $e->getMessage());
            }
        }
    }
}
