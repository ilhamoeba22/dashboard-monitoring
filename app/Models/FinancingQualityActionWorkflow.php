<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancingQualityActionWorkflow extends Model
{
    protected $fillable = [
        'period_year',
        'period_month',
        'action_key',
        'nokontrak',
        'nama',
        'source',
        'signals',
        'severity',
        'score',
        'exposure',
        'status',
        'owner',
        'due_date',
        'note',
        'completed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'period_year' => 'integer',
        'period_month' => 'integer',
        'signals' => 'array',
        'score' => 'integer',
        'exposure' => 'float',
        'due_date' => 'date:Y-m-d',
        'completed_at' => 'datetime',
    ];
}
