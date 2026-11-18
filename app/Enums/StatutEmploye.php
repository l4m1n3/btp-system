<?php

namespace App\Enums;

enum StatutEmploye: string
{
    use EnumHelpers;

    case Actif = 'actif';
    case Suspendu = 'suspendu';
    case Sorti = 'sorti';

    public function label(): string
    {
        return match ($this) {
            self::Actif => 'Actif',
            self::Suspendu => 'Suspendu',
            self::Sorti => 'Sorti',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Actif => 'green',
            self::Suspendu => 'orange',
            self::Sorti => 'slate',
        };
    }
}
