<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RamadanSchedule extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'is_public' => 'boolean',
    ];
}