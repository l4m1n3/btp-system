<?php

namespace App\Enums;

enum Role: string
{
    use EnumHelpers;

    case Admin = 'admin';
    case Direction = 'direction';
    case Comptable = 'comptable';
    case ChefChantier = 'chef_chantier';
    case Rh = 'rh';
    case Logistique = 'logistique';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrateur',
            self::Direction => 'Direction',
            self::Comptable => 'Comptable',
            self::ChefChantier => 'Chef de chantier',
            self::Rh => 'Ressources humaines',
            self::Logistique => 'Logistique',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Admin => 'red',
            self::Direction => 'violet',
            self::Comptable => 'blue',
            self::ChefChantier => 'amber',
            self::Rh => 'teal',
            self::Logistique => 'slate',
        };
    }
}
