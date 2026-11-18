<?php

namespace App\Models;

use App\Enums\StatutDecompte;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Decompte extends Model
{
    protected $fillable = [
        'marche_id', 'numero', 'date_decompte', 'montant_ht', 'montant_paye',
        'date_paiement', 'statut', 'observations',
    ];

    protected function casts(): array
    {
        return [
            'statut' => StatutDecompte::class,
            'date_decompte' => 'date',
            'date_paiement' => 'date',
            'montant_ht' => 'decimal:2',
            'montant_paye' => 'decimal:2',
        ];
    }

    public function marche(): BelongsTo
    {
        return $this->belongsTo(Marche::class);
    }
}
