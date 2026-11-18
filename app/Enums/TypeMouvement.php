<?php

namespace App\Enums;

enum TypeMouvement: string
{
    use EnumHelpers;

    case Entree = 'entree';
    case Sortie = 'sortie';
    case Ajustement = 'ajustement';

    public function label(): string
    {
        return match ($this) {
            self::Entree => 'Entrée',
            self::Sortie => 'Sortie chantier',
            self::Ajustement => 'Ajustement inventaire',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Entree => 'green',
            self::Sortie => 'blue',
            self::Ajustement => 'slate',
        };
    }
}
