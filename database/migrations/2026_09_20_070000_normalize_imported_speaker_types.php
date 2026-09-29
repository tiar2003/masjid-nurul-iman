<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('speakers')
            ->where('type', 'Khutbah (Jumat)')
            ->update(['type' => 'Khutbah']);

        DB::table('speakers')
            ->whereNull('limit_once')
            ->update(['limit_once' => 0]);
    }

    public function down()
    {
        // Data yang sudah dinormalisasi tidak dikembalikan ke format impor lama.
    }
};
