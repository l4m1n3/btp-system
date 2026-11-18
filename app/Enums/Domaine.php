<?php

namespace App\Enums;

enum Domaine: string
{
    use EnumHelpers;

    case Batiment = 'batiment';
    case TravauxPublics = 'travaux_publics';
    case Hydraulique = 'hydraulique';
    case Autre = 'autre';

    public function label(): string
    {
        return match ($this) {
            self::Batiment => 'Bâtiment',
            self::TravauxPublics => 'Travaux publics',
            self::Hydraulique => 'Hydraulique',
            self::Autre => 'Autre',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Batiment => 'blue',
            self::TravauxPublics => 'amber',
            self::Hydraulique => 'cyan',
            self::Autre => 'slate',
        };
    }
}
