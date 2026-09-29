<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrphanRecipient extends Model
{
    protected $guarded = [];

    public function distributions()
    {
        return $this->hasMany(OrphanDistribution::class, 'recipient_id');
    }
}