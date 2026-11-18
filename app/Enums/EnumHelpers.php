<?php

namespace App\Enums;

trait EnumHelpers
{
    /** @return array<string,string> valeur => libellé */
    public static function options(): array
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[$case->value] = $case->label();
        }

        return $out;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function color(): string
    {
        return 'slate';
    }
}
