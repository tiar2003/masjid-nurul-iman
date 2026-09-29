<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MosqueLetter extends Model
{
    protected $guarded = [];

    protected $casts = [
        'issue_date' => 'date',
        'field_data' => 'array',
    ];

    public function template()
    {
        return $this->belongsTo(LetterTemplate::class, 'template_id');
    }
}