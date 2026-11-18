<?php

namespace App\Enums;

enum StatutConge: string
{
    use EnumHelpers;

    case Demande = 'demande';
    case Approuve = 'approuve';
    case Refuse = 'refuse';

    public function label(): string
    {
        return match ($this) {
            self::Demande => 'Demandé',
            self::Approuve => 'Approuvé',
            self::Refuse => 'Refusé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Demande => 'amber',
            self::Approuve => 'green',
            self::Refuse => 'red',
        };
    }
}
