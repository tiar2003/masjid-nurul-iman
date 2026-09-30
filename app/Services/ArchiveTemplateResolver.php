<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ArchiveTemplateResolver
{
    public function resolve(?string $sourcePath): ?array
    {
        if (blank($sourcePath)) {
            return null;
        }

        if (is_file($sourcePath)) {
            return [$sourcePath, false];
        }

        $localPath = Storage::disk('local')->path($sourcePath);
        if (is_file($localPath)) {
            return [$localPath, false];
        }

        $archivePath = storage_path('app/referensi-masjid/MASJID.zip');
        if (! is_file($archivePath)) {
            return null;
        }

        $archive = new ZipArchive();
        if ($archive->open($archivePath) !== true) {
            return null;
        }

        $contents = $archive->getFromName($sourcePath);
        $archive->close();
        if ($contents === false) {
            return null;
        }

        $temporaryPath = tempnam(sys_get_temp_dir(), 'masjid_archive_template_');
        if ($temporaryPath === false || file_put_contents($temporaryPath, $contents) === false) {
            return null;
        }

        return [$temporaryPath, true];
    }
}
