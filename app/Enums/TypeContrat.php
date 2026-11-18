<?php

namespace App\Enums;

enum TypeContrat: string
{
    use EnumHelpers;

    case Cdi = 'cdi';
    case Cdd = 'cdd';
    case Journalier = 'journalier';
    case Stage = 'stage';

    public function label(): string
    {
        return match ($this) {
            self::Cdi => 'CDI',
            self::Cdd => 'CDD',
            self::Journalier => 'Journalier',
            self::Stage => 'Stage',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Cdi => 'green',
            self::Cdd => 'blue',
            self::Journalier => 'amber',
            self::Stage => 'slate',
        };
    }
}
