<?php

namespace App\Enums;

enum TypeLigneJournal: string
{
    use EnumHelpers;

    case Personnel = 'personnel';
    case Materiel = 'materiel';
    case Approvisionnement = 'approvisionnement';
    case Travaux = 'travaux';

    public function label(): string
    {
        return match ($this) {
            self::Personnel => 'Personnel',
            self::Materiel => 'Matériel',
            self::Approvisionnement => 'Approvisionnement',
            self::Travaux => 'Travaux réalisés',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Personnel => 'blue',
            self::Materiel => 'amber',
            self::Approvisionnement => 'teal',
            self::Travaux => 'green',
        };
    }
}
