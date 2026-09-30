<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $paths = [
            'ZAKAT_EDARAN_2025' => 'MASJID/ROMADON/Zakat/EDARAN ZAKAT DAN TANDA TERIMA/SURAT EDARAN ZAKAT FITRAH.docx',
            'ZAKAT_FINAL_2025' => 'MASJID/ROMADON/Zakat/EDARAN ZAKAT DAN TANDA TERIMA/HASIL AKHIR ZAKAT FITRAH.docx',
            'ZAKAT_HANDOVER_2025' => 'MASJID/ROMADON/Zakat/EDARAN ZAKAT DAN TANDA TERIMA/TANDA PENYERAHAN ZAKAT FITRAH.docx',
        ];

        foreach ($paths as $key => $path) {
            DB::table('letter_templates')->where('template_key', $key)->update(['source_path' => $path]);
        }
    }

    public function down(): void
    {
        // Archive paths are corrected data and are intentionally not reverted.
    }
};