<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZakatReport extends Model
{
    protected $guarded = [];

    protected $casts = [
        'summary' => 'array',
    ];

    public function period()
    {
        return $this->belongsTo(ZakatPeriod::class, 'period_id');
    }
}