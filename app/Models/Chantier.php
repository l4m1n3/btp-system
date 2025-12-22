<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chantier extends Model
{
     protected $fillable = [
        'nom',
        'budget_total',
        'patron_id'
    ];
}