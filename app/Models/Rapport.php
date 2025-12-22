<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rapport extends Model
{
   protected $fillable = [
        'chantier_id',
        'semaine',
        'pdf_path'
    ];
}
