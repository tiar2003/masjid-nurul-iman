<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $archivePath = storage_path('app/referensi-masjid/MASJID.zip');
        $archive = new \ZipArchive();
        $archiveReady = is_file($archivePath) && $archive->open($archivePath) === true;

        if (! $archiveReady) {
            return;
        }

        $templates = DB::table('letter_templates')->get();
        foreach ($templates as $template) {
            if ($archive->locateName($template->source_path) === false) {
                continue;
            }

            DB::table('letter_templates')->where('id', $template->id)->update([
                'is_verified' => true,
                'is_active' => true,
                'updated_at' => now(),
            ]);
        }

        $archive->close();
    }

    public function down(): void
    {
        DB::table('letter_templates')->update([
            'is_verified' => false,
            'is_active' => false,
            'updated_at' => now(),
        ]);

        DB::table('letter_templates')->where('template_key', 'ZAKAT_EDARAN_2025')->update([
            'is_verified' => true,
            'is_active' => true,
            'updated_at' => now(),
        ]);
    }
};
