<?php

namespace App\Exports;

use App\Models\Depense;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepensesExport implements FromQuery, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private Builder $requete) {}

    public function query(): Builder
    {
        return $this->requete;
    }

    public function headings(): array
    {
        return ['Date', 'Type', 'Chantier', 'Catégorie', 'Libellé', 'Fournisseur', 'N° pièce', 'Mode de paiement', 'Montant (FCFA)', 'Statut'];
    }

    /** @param Depense $d */
    public function map($d): array
    {
        return [
            Date::dateTimeToExcel($d->date_depense),
            $d->portee->label(),
            $d->chantier?->nom ?? '',
            $d->categorie->nom,
            $d->libelle,
            $d->fournisseur?->nom ?? '',
            $d->reference_piece ?? '',
            $d->mode_paiement->label(),
            (float) $d->montant,
            $d->statut->label(),
        ];
    }

    public function columnFormats(): array
    {
        return ['A' => 'dd/mm/yyyy', 'I' => '#,##0'];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->freezePane('A2');

        return [1 => ['font' => ['bold' => true]]];
    }
}
