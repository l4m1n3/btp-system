<?php

namespace App\Enums;

enum TypeConge: string
{
    use EnumHelpers;

    case CongeAnnuel = 'conge_annuel';
    case Maladie = 'maladie';
    case Permission = 'permission';
    case AbsenceInjustifiee = 'absence_injustifiee';

    public function label(): string
    {
        return match ($this) {
            self::CongeAnnuel => 'Congé annuel',
            self::Maladie => 'Maladie',
            self::Permission => 'Permission',
            self::AbsenceInjustifiee => 'Absence injustifiée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::CongeAnnuel => 'blue',
            self::Maladie => 'teal',
            self::Permission => 'slate',
            self::AbsenceInjustifiee => 'red',
        };
    }
}
