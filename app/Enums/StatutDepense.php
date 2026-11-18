<?php

namespace App\Enums;

enum StatutDepense: string
{
    use EnumHelpers;

    case EnAttente = 'en_attente';
    case Validee = 'validee';
    case Rejetee = 'rejetee';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::Validee => 'Validée',
            self::Rejetee => 'Rejetée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EnAttente => 'amber',
            self::Validee => 'green',
            self::Rejetee => 'red',
        };
    }
}
