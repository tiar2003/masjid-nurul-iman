<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Terawih extends Model
{
    protected $table = 'terawih';

    // Memberitahu Laravel bahwa tabel ini tidak pakai kolom 'id'
    protected $primaryKey = 'tanggal';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];
    public $timestamps = false;
}
