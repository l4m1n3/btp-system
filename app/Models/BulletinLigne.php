<?php

namespace App\Models;

use App\Enums\TypeLignePaie;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulletinLigne extends Model
{
    protected $fillable = ['bulletin_id', 'type', 'libelle', 'montant'];

    protected function casts(): array
    {
        return ['type' => TypeLignePaie::class, 'montant' => 'decimal:2'];
    }

    public function bulletin(): BelongsTo
    {
        return $this->belongsTo(Bulletin::class);
    }
}
