<?php

it('formate les montants avec des espaces comme séparateur de milliers', function () {
    expect(fcfa(1250000.4, false))->toBe('1 250 000')
        ->and(fcfa(null, false))->toBe('0');
});
