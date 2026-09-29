<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('letter_templates')->where('template_key', 'MOSQUE_OFFICIAL_LETTER')->update([
            'numbering_key' => 'MNI',
            'numbering_pattern' => '{seq3}/MNI/I/{month_roman}/{year}',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('letter_templates')->where('template_key', 'MOSQUE_OFFICIAL_LETTER')->update([
            'numbering_key' => 'mni',
            'numbering_pattern' => null,
            'updated_at' => now(),
        ]);
    }
};