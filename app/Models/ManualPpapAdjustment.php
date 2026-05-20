<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualPpapAdjustment extends Model
{
    protected $fillable = ['nokontrak', 'nominal_ppap', 'alasan', 'created_by'];
}
