<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistribusiZakat extends Model
{
    use HasFactory;
    // Tambahkan baris ini agar fitur mass-assignment diizinkan
    protected $guarded = [];
}
