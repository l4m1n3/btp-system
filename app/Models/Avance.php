<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avance extends Model
{
    protected $fillable = ['employe_id', 'date_avance', 'montant', 'motif', 'periode_retenue', 'bulletin_id'];

    protected function casts(): array
    {
        return ['date_avance' => 'date', 'montant' => 'decimal:2'];
    }

    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class);
    }

    public function bulletin(): BelongsTo
    {
        return $this->belongsTo(Bulletin::class);
    }
}
