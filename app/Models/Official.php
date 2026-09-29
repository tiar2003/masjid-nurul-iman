<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Official extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_signatory' => 'boolean',
        'is_active' => 'boolean',
    ];
}