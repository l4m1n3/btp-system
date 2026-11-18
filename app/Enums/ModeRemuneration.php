<?php

namespace App\Enums;

enum ModeRemuneration: string
{
    use EnumHelpers;

    case Mensuel = 'mensuel';
    case Journalier = 'journalier';

    public function label(): string
    {
        return match ($this) {
            self::Mensuel => 'Mensuel',
            self::Journalier => 'Journalier',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Mensuel => 'blue',
            self::Journalier => 'amber',
        };
    }
}
