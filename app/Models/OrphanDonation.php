<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrphanDonation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];
}