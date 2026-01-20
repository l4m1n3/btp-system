<?php

namespace App\Imports;

use App\Models\Devis;
use App\Models\DevisLigne;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class DevisImport implements ToCollection, WithCalculatedFormulas
{
    protected Devis $devis;

    public function __construct(Devis $devis)
    {
        $this->devis = $devis;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
      foreach ($rows as $index => $row) {

            // ignorer entête
            if ($index < 1) continue;

           $designation = mb_substr(trim($row[0] ?? ''), 0, 10000);
            if ($designation === '') continue;

            $unite      = $row[1] ?? null;
            $quantite   = $this->toNumber($row[2] ?? null);
            $prixUnit   = $this->toNumber($row[3] ?? null);

            // ⚠️ montant peut être une formule → recalcul backend
            $montant = ($quantite !== null && $prixUnit !== null)
                ? $quantite * $prixUnit
                : $this->toNumber($row[4] ?? null);

            DevisLigne::create([
                'devis_id'      => $this->devis->id,
                'designation'   => $designation,
                'unite'         => $unite,
                'quantite'      => $quantite,
                'prix_unitaire' => $prixUnit,
                'montant'       => $montant,
            ]);
        }
    }
     private function toNumber($value)
    {
        if ($value === null) return null;

        // si Excel renvoie encore une formule
        if (is_string($value) && str_starts_with($value, '=')) {
            return null;
        }

        $value = str_replace([' ', ','], ['', '.'], $value);

        return is_numeric($value) ? (float)$value : null;
    }
}
