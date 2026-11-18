<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    public const TYPES = ['preventive' => 'Préventive', 'corrective' => 'Corrective (panne)'];

    protected $fillable = ['materiel_id', 'date_maintenance', 'type', 'description', 'cout', 'depense_id'];

    protected function casts(): array
    {
        return ['date_maintenance' => 'date', 'cout' => 'decimal:2'];
    }

    public function materiel(): BelongsTo
    {
        return $this->belongsTo(Materiel::class);
    }

    public function depense(): BelongsTo
    {
        return $this->belongsTo(Depense::class);
    }
}
