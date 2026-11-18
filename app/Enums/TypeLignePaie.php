<?php

namespace App\Enums;

enum TypeLignePaie: string
{
    use EnumHelpers;

    case Prime = 'prime';
    case Indemnite = 'indemnite';
    case Retenue = 'retenue';

    public function label(): string
    {
        return match ($this) {
            self::Prime => 'Prime',
            self::Indemnite => 'Indemnité',
            self::Retenue => 'Retenue',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Prime => 'green',
            self::Indemnite => 'teal',
            self::Retenue => 'red',
        };
    }
}
