<?php

if (! function_exists('fcfa')) {
    /** Formate un montant : 1 250 000 FCFA */
    function fcfa(float|int|string|null $montant, bool $devise = true): string
    {
        $txt = number_format((float) $montant, 0, ',', ' ');

        return $devise ? $txt.' '.config('btp.devise', 'FCFA') : $txt;
    }
}

if (! function_exists('date_fr')) {
    function date_fr($date, string $format = 'd/m/Y'): string
    {
        return $date ? \Illuminate\Support\Carbon::parse($date)->format($format) : '—';
    }
}
