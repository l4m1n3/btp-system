<?php

namespace App\Models;

use App\Enums\ModeRemuneration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bulletin extends Model
{
    protected $fillable = [
        'periode_paie_id', 'employe_id', 'chantier_id', 'mode_remuneration', 'salaire_base',
        'jours_travailles', 'jours_absence', 'salaire_du', 'heures_sup', 'montant_heures_sup',
        'total_primes', 'salaire_brut', 'total_avances', 'total_retenues', 'net_a_payer',
        'paye', 'date_paiement',
    ];

    protected function casts(): array
    {
        return [
            'mode_remuneration' => ModeRemuneration::class,
            'paye' => 'boolean',
            'date_paiement' => 'date',
            'salaire_base' => 'decimal:2',
            'salaire_du' => 'decimal:2',
            'montant_heures_sup' => 'decimal:2',
            'total_primes' => 'decimal:2',
            'salaire_brut' => 'decimal:2',
            'total_avances' => 'decimal:2',
            'total_retenues' => 'decimal:2',
            'net_a_payer' => 'decimal:2',
        ];
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PeriodePaie::class, 'periode_paie_id');
    }

    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class);
    }

    public function chantier(): BelongsTo
    {
        return $this->belongsTo(Chantier::class);
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(BulletinLigne::class);
    }

    public function avances(): HasMany
    {
        return $this->hasMany(Avance::class);
    }
}
