<?php

namespace App\Models;

use App\Enums\TypeLigneJournal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RapportJournalierLigne extends Model
{
    protected $fillable = ['rapport_journalier_id', 'type', 'designation', 'quantite', 'unite', 'tache_id', 'avancement'];

    protected function casts(): array
    {
        return ['type' => TypeLigneJournal::class, 'quantite' => 'decimal:2'];
    }

    public function rapport(): BelongsTo
    {
        return $this->belongsTo(RapportJournalier::class, 'rapport_journalier_id');
    }

    public function tache(): BelongsTo
    {
        return $this->belongsTo(Tache::class);
    }
}
