<?php

namespace App\Enums;

enum PorteeDepense: string
{
    use EnumHelpers;

    case Interne = 'interne';
    case Chantier = 'chantier';

    public function label(): string
    {
        return match ($this) {
            self::Interne => 'Interne (frais généraux)',
            self::Chantier => 'Chantier',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Interne => 'violet',
            self::Chantier => 'blue',
        };
    }
}
