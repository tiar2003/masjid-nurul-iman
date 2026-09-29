<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZakatReceipt extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'fitrah_rice_kg' => 'decimal:2',
        'fitrah_money' => 'decimal:2',
        'zakat_mal' => 'decimal:2',
        'fidyah_rice_kg' => 'decimal:2',
        'fidyah_money' => 'decimal:2',
        'infaq' => 'decimal:2',
        'shodaqoh' => 'decimal:2',
    ];

    public function period()
    {
        return $this->belongsTo(ZakatPeriod::class, 'period_id');
    }
}