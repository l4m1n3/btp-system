<?php

namespace App\Enums;

enum StatutTache: string
{
    use EnumHelpers;

    case Planifie = 'planifie';
    case EnCours = 'en_cours';
    case Termine = 'termine';
    case Suspendu = 'suspendu';

    public function label(): string
    {
        return match ($this) {
            self::Planifie => 'Planifiée',
            self::EnCours => 'En cours',
            self::Termine => 'Terminée',
            self::Suspendu => 'Suspendue',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Planifie => 'slate',
            self::EnCours => 'blue',
            self::Termine => 'green',
            self::Suspendu => 'orange',
        };
    }
}
