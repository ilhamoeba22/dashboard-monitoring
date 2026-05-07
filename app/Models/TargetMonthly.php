<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TargetMonthly extends Model
{
    protected $fillable = [
        'annual_target_id',
        'month',
        'nominal_target',
    ];

    /**
     * Get the annual target that owns the monthly target.
     */
    public function annualTarget(): BelongsTo
    {
        return $this->belongsTo(TargetAnnual::class, 'annual_target_id');
    }
}
