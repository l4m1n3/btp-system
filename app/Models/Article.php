<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    protected $fillable = ['code', 'designation', 'unite', 'stock_minimum', 'stock_actuel', 'cout_moyen'];

    protected function casts(): array
    {
        return [
            'stock_minimum' => 'decimal:2',
            'stock_actuel' => 'decimal:2',
            'cout_moyen' => 'decimal:2',
        ];
    }

    public function mouvements(): HasMany
    {
        return $this->hasMany(MouvementStock::class)->orderByDesc('date_mouvement')->orderByDesc('id');
    }

    public function scopeSousMinimum(Builder $query): Builder
    {
        return $query->whereColumn('stock_actuel', '<=', 'stock_minimum')->where('stock_minimum', '>', 0);
    }

    public function valeurStock(): float
    {
        return round((float) $this->stock_actuel * (float) $this->cout_moyen, 2);
    }
}
