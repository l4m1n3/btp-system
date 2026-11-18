<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RapportJournalier extends Model
{
    protected $table = 'rapports_journaliers';

    protected $fillable = ['chantier_id', 'date_rapport', 'meteo', 'observations', 'incidents', 'redige_par'];

    protected function casts(): array
    {
        return ['date_rapport' => 'date'];
    }

    public function chantier(): BelongsTo
    {
        return $this->belongsTo(Chantier::class);
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redige_par');
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(RapportJournalierLigne::class);
    }
}
