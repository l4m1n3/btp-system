<?php

namespace App\Enums;

enum EtatMateriel: string
{
    use EnumHelpers;

    case Disponible = 'disponible';
    case Affecte = 'affecte';
    case EnPanne = 'en_panne';
    case EnMaintenance = 'en_maintenance';
    case HorsService = 'hors_service';

    public function label(): string
    {
        return match ($this) {
            self::Disponible => 'Disponible',
            self::Affecte => 'Affecté',
            self::EnPanne => 'En panne',
            self::EnMaintenance => 'En maintenance',
            self::HorsService => 'Hors service',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Disponible => 'green',
            self::Affecte => 'blue',
            self::EnPanne => 'red',
            self::EnMaintenance => 'orange',
            self::HorsService => 'slate',
        };
    }
}
