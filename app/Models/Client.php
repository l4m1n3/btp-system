<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    public const TYPES = ['public' => 'Public / État', 'prive' => 'Privé', 'ong' => 'ONG / Projet'];

    protected $fillable = ['nom', 'type', 'contact', 'telephone', 'email', 'adresse', 'notes'];

    public function marches(): HasMany
    {
        return $this->hasMany(Marche::class);
    }
}
