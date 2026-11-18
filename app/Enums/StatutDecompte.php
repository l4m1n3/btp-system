<?php

namespace App\Enums;

enum StatutDecompte: string
{
    use EnumHelpers;

    case Soumis = 'soumis';
    case Valide = 'valide';
    case Paye = 'paye';
    case Rejete = 'rejete';

    public function label(): string
    {
        return match ($this) {
            self::Soumis => 'Soumis',
            self::Valide => 'Validé',
            self::Paye => 'Payé',
            self::Rejete => 'Rejeté',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Soumis => 'amber',
            self::Valide => 'blue',
            self::Paye => 'green',
            self::Rejete => 'red',
        };
    }
}
