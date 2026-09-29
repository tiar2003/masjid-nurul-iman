<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LetterTemplate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'margins_mm' => 'array',
        'fields' => 'array',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function letters()
    {
        return $this->hasMany(MosqueLetter::class, 'template_id');
    }
}