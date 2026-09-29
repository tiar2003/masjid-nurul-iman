<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QurbanContribution extends Model
{
    protected $guarded = [];

    protected $casts = [
        'paid_at' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];
}