<?php

namespace App\Services;

use App\Enums\TypeLigneJournal;
use App\Models\Chantier;
use App\Models\RapportJournalier;
use App\Models\Tache;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Enregistrement d'un rapport journalier (web et API).
 * Les lignes « travaux » liées à une tâche font progresser son avancement (jamais reculer).
 */
class JournalService
{
    public static function regles(Chantier $chantier, ?RapportJournalier $rapport = null): array
    {
        return [
            'date_rapport' => ['required', 'date', 'before_or_equal:today', function ($attribut, $valeur, $echec) use ($chantier, $rapport) {
                $existe = RapportJournalier::where('chantier_id', $chantier->id)->whereDate('date_rapport', $valeur)
                    ->when($rapport?->exists, fn ($q) => $q->whereKeyNot($rapport->id))->exists();
                if ($existe) {
                    $echec('Un rapport existe déjà pour ce chantier à cette date.');
                }
            }],
            'meteo' => 'nullable|string|max:100',
            'observations' => 'nullable|string',
            'incidents' => 'nullable|string',
            'lignes' => 'array',
            'lignes.*.type' => ['required', Rule::enum(TypeLigneJournal::class)],
            'lignes.*.designation' => 'required|string|max:255',
            'lignes.*.quantite' => 'nullable|numeric|min:0',
            'lignes.*.unite' => 'nullable|string|max:30',
            'lignes.*.tache_id' => ['nullable', Rule::exists('taches', 'id')->where('chantier_id', $chantier->id)],
            'lignes.*.avancement' => 'nullable|integer|between:0,100',
        ];
    }

    public function enregistrer(Chantier $chantier, array $data, User $auteur, ?RapportJournalier $rapport = null): RapportJournalier
    {
        return DB::transaction(function () use ($chantier, $data, $auteur, $rapport) {
            $rapport ??= new RapportJournalier(['chantier_id' => $chantier->id, 'redige_par' => $auteur->id]);
            $rapport->fill([
                'date_rapport' => $data['date_rapport'],
                'meteo' => ($data['meteo'] ?? null) ?: null,
                'observations' => ($data['observations'] ?? null) ?: null,
                'incidents' => ($data['incidents'] ?? null) ?: null,
            ])->save();

            $rapport->lignes()->delete();
            foreach ($data['lignes'] ?? [] as $l) {
                $l = array_map(fn ($v) => $v === '' ? null : $v, $l);
                $rapport->lignes()->create([
                    'type' => $l['type'], 'designation' => $l['designation'], 'quantite' => $l['quantite'] ?? null,
                    'unite' => $l['unite'] ?? null, 'tache_id' => $l['tache_id'] ?? null, 'avancement' => $l['avancement'] ?? null,
                ]);
                if ($l['type'] === TypeLigneJournal::Travaux->value && ! empty($l['tache_id']) && isset($l['avancement'])) {
                    $tache = Tache::find($l['tache_id']);
                    if ((int) $l['avancement'] > $tache->avancement) {
                        $tache->update(['avancement' => (int) $l['avancement']]);
                    }
                }
            }

            return $rapport->load('lignes');
        });
    }
}
