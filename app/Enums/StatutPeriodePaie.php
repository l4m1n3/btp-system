<?php

namespace App\Enums;

enum StatutPeriodePaie: string
{
    use EnumHelpers;

    case Brouillon = 'brouillon';
    case Cloturee = 'cloturee';

    public function label(): string
    {
        return match ($this) {
            self::Brouillon => 'Brouillon',
            self::Cloturee => 'Clôturée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Brouillon => 'amber',
            self::Cloturee => 'green',
        };
    }
}
