<?php

namespace App\Models;

use App\Enums\PorteeDepense;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategorieDepense extends Model
{
    protected $table = 'categories_depenses';

    public const SALAIRES = 'Salaires et main-d\'œuvre';

    protected $fillable = ['nom', 'portee', 'description', 'systeme'];

    protected function casts(): array
    {
        return ['portee' => PorteeDepense::class, 'systeme' => 'boolean'];
    }

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class, 'categorie_id');
    }

    public static function salaires(PorteeDepense $portee): self
    {
        return self::firstOrCreate(
            ['nom' => self::SALAIRES, 'portee' => $portee->value],
            ['systeme' => true, 'description' => 'Générée automatiquement à la clôture de la paie'],
        );
    }
}
