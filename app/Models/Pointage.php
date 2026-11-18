<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pointage extends Model
{
    protected $fillable = ['employe_id', 'chantier_id', 'date_pointage', 'present', 'heures_sup', 'observation', 'saisi_par'];

    protected function casts(): array
    {
        return ['date_pointage' => 'date', 'present' => 'boolean', 'heures_sup' => 'decimal:2'];
    }

    /** Pointage existant d'un employé pour un jour donné, ou nouvelle instance (indépendant du format de date en base). */
    public static function pour(int $employeId, string $date): self
    {
        return self::where('employe_id', $employeId)->whereDate('date_pointage', $date)->first()
            ?? new self(['employe_id' => $employeId, 'date_pointage' => $date]);
    }

    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class);
    }

    public function chantier(): BelongsTo
    {
        return $this->belongsTo(Chantier::class);
    }
}
