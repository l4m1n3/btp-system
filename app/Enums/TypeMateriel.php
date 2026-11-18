<?php

namespace App\Enums;

enum TypeMateriel: string
{
    use EnumHelpers;

    case Engin = 'engin';
    case Vehicule = 'vehicule';
    case Equipement = 'equipement';
    case Outillage = 'outillage';

    public function label(): string
    {
        return match ($this) {
            self::Engin => 'Engin',
            self::Vehicule => 'Véhicule',
            self::Equipement => 'Équipement',
            self::Outillage => 'Outillage',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Engin => 'amber',
            self::Vehicule => 'blue',
            self::Equipement => 'teal',
            self::Outillage => 'slate',
        };
    }
}
