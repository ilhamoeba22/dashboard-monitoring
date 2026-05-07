<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TargetAnnual extends Model
{
    protected $fillable = [
        'dimension_type',
        'dimension_id',
        'target_year',
        'total_nominal',
    ];

    /**
     * Get the monthly targets for this annual target.
     */
    public function monthlyTargets(): HasMany
    {
        return $this->hasMany(TargetMonthly::class, 'annual_target_id');
    }
}
