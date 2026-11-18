<?php

namespace App\Models;

use App\Enums\TypeMouvement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementStock extends Model
{
    protected $table = 'mouvements_stock';

    protected $fillable = [
        'article_id', 'type', 'quantite', 'prix_unitaire', 'chantier_id', 'fournisseur_id',
        'date_mouvement', 'reference', 'observation', 'saisi_par',
    ];

    protected function casts(): array
    {
        return [
            'type' => TypeMouvement::class,
            'quantite' => 'decimal:2',
            'prix_unitaire' => 'decimal:2',
            'date_mouvement' => 'date',
        ];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function chantier(): BelongsTo
    {
        return $this->belongsTo(Chantier::class);
    }

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function getValeurAttribute(): float
    {
        return round((float) $this->quantite * (float) $this->prix_unitaire, 2);
    }
}
