<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrphanDistribution extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function recipient()
    {
        return $this->belongsTo(OrphanRecipient::class, 'recipient_id');
    }
}