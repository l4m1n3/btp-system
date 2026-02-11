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
    // private function envoyerRapport(Carbon $start, Carbon $end, string $type): void
    // {
    //     $depenses = Depense::whereBetween('date_depense', [$start, $end])
    //         ->with('chantier')
    //         ->get();

    //     if ($depenses->isEmpty()) {
    //         Log::info("Aucune dépense pour le rapport $type entre $start et $end.");
    //         return;
    //     }

    //     $patron = Patron::first();
    //     if (!$patron) {
    //         Log::error("Pas de patron trouvé pour le rapport $type.");
    //         return;
    //     }

    //     // Génération PDF
    //     $pdf = Pdf::loadView('pdf.rapport', [
    //         'depenses' => $depenses,
    //         'start' => $start,
    //         'end' => $end,
    //         'patron' => $patron,
    //         'type' => $type,
    //     ]);

    //     $folder = storage_path('app/rapports');
    //     if (!file_exists($folder)) {
    //         mkdir($folder, 0755, true);
    //     }

    //     $fileName = "rapport_{$type}_" . now()->format('Y_m_d_H_i_s') . ".pdf";
    //     $filePath = $folder . '/' . $fileName;
    //     file_put_contents($filePath, $pdf->output());
    //     Log::info("Rapport $type généré : $fileName");

    //     // Envoi email
    //     if ($patron->email) {
    //         try {
    //             Mail::to($patron->email)->send(new RapportHebdomadaireMail($filePath));
    //             Log::info("Rapport $type envoyé par email à {$patron->email}");
    //         } catch (\Exception $e) {
    //             Log::error("Erreur email rapport $type : " . $e->getMessage());
    //         }
    //     }
    // }
    private function envoyerRapport(Carbon $start, Carbon $end, string $type): void
    {
        // Récupérer tous les patrons avec abonnement actif
        $patrons = Patron::where('date_fin', '>', now())
            ->orWhereNull('date_fin')
            ->get();

        if ($patrons->isEmpty()) {
            Log::info("Aucun patron avec abonnement actif pour le rapport $type.");
            return;
        }

        Log::info("Envoi du rapport $type à " . $patrons->count() . " patron(s) avec abonnement actif.");

        foreach ($patrons as $patron) {
            try {
                // Récupérer les dépenses liées aux chantiers de ce patron
                $depenses = Depense::whereBetween('date_depense', [$start, $end])
                    ->whereHas('chantier', function ($query) use ($patron) {
                        $query->where('entreprise_id', $patron->id);
                    })
                    ->with('chantier')
                    ->get();

                if ($depenses->isEmpty()) {
                    Log::info("Aucune dépense pour le patron {$patron->name} (ID: {$patron->id}) entre $start et $end.");
                    continue;
                }

                // Génération du PDF pour ce patron
                $pdf = Pdf::loadView('pdf.rapport', [
                    'depenses' => $depenses,
                    'start' => $start,
                    'end' => $end,
                    'patron' => $patron,
                    'type' => $type,
                ]);

                // Créer le dossier s'il n'existe pas
                $folder = storage_path('app/rapports');
                if (!file_exists($folder)) {
                    mkdir($folder, 0755, true);
                }

                // Nom du fichier avec l'ID du patron pour éviter les conflits
                $fileName = "rapport_{$type}_patron_{$patron->id}_" . now()->format('Y_m_d_H_i_s') . ".pdf";
                $filePath = $folder . '/' . $fileName;
                file_put_contents($filePath, $pdf->output());

                Log::info("Rapport $type généré pour le patron {$patron->name} : $fileName");

                // Envoi email si le patron a une adresse email
                if ($patron->email) {
                    try {
                        Mail::to($patron->email)->send(new RapportHebdomadaireMail($filePath, $patron));
                        Log::info("Rapport $type envoyé par email à {$patron->email} (Patron: {$patron->name})");
                    } catch (\Exception $e) {
                        Log::error("Erreur email rapport $type pour {$patron->name} : " . $e->getMessage());
                    }
                } else {
                    Log::warning("Le patron {$patron->name} (ID: {$patron->id}) n'a pas d'adresse email.");
                }

                // Optionnel : Supprimer le fichier après envoi pour économiser l'espace
                // if (file_exists($filePath)) {
                //     unlink($filePath);
                //     Log::info("Fichier PDF supprimé après envoi : $fileName");
                // }

            } catch (\Exception $e) {
                Log::error("Erreur lors de la génération/envoi du rapport $type pour le patron {$patron->name} (ID: {$patron->id}) : " . $e->getMessage());
                continue; // Continuer avec les autres patrons
            }
        }

        Log::info("Fin de l'envoi des rapports $type.");
    }
}
