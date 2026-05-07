<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetAdjustment extends Model
{
    protected $fillable = [
        'dimension_type',
        'from_dimension_id',
        'to_dimension_id',
        'effective_month',
        'target_year',
        'nominal_transferred',
        'reason',
        'created_by',
    ];
}
