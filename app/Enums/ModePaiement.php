<?php

namespace App\Enums;

enum ModePaiement: string
{
    use EnumHelpers;

    case Especes = 'especes';
    case Cheque = 'cheque';
    case Virement = 'virement';
    case MobileMoney = 'mobile_money';

    public function label(): string
    {
        return match ($this) {
            self::Especes => 'Espèces',
            self::Cheque => 'Chèque',
            self::Virement => 'Virement',
            self::MobileMoney => 'Mobile money',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Especes => 'slate',
            self::Cheque => 'slate',
            self::Virement => 'slate',
            self::MobileMoney => 'slate',
        };
    }
}
