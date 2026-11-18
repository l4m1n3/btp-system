<?php

namespace App\Enums;

enum StatutMarche: string
{
    use EnumHelpers;

    case EnAttente = 'en_attente';
    case EnCours = 'en_cours';
    case Suspendu = 'suspendu';
    case Termine = 'termine';
    case Annule = 'annule';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::EnCours => 'En cours',
            self::Suspendu => 'Suspendu',
            self::Termine => 'Terminé',
            self::Annule => 'Annulé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EnAttente => 'amber',
            self::EnCours => 'blue',
            self::Suspendu => 'orange',
            self::Termine => 'green',
            self::Annule => 'slate',
        };
    }
}
