<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fournisseur extends Model
{
    protected $fillable = ['nom', 'nif', 'telephone', 'email', 'adresse'];

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class);
    }
}
