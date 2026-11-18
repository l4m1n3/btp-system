<?php

namespace App\Enums;

enum StatutChantier: string
{
    use EnumHelpers;

    case EnPreparation = 'en_preparation';
    case EnCours = 'en_cours';
    case Suspendu = 'suspendu';
    case Termine = 'termine';

    public function label(): string
    {
        return match ($this) {
            self::EnPreparation => 'En préparation',
            self::EnCours => 'En cours',
            self::Suspendu => 'Suspendu',
            self::Termine => 'Terminé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EnPreparation => 'amber',
            self::EnCours => 'blue',
            self::Suspendu => 'orange',
            self::Termine => 'green',
        };
    }
}
