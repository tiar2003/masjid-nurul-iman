<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZakatPeriod extends Model
{
    protected $guarded = [];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'fitrah_kg_per_person' => 'decimal:2',
        'zakat_mal_rate_percent' => 'decimal:2',
        'fidyah_kg_per_day' => 'decimal:2',
        'fidyah_money_per_day' => 'decimal:2',
    ];

    public function receipts()
    {
        return $this->hasMany(ZakatReceipt::class, 'period_id');
    }

    public function distributions()
    {
        return $this->hasMany(ZakatDistribution::class, 'period_id');
    }

    public function reports()
    {
        return $this->hasMany(ZakatReport::class, 'period_id');
    }
}