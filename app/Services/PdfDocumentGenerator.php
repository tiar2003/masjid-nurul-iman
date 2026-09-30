<?php

namespace App\Services;

use Symfony\Component\Process\Process;

class PdfDocumentGenerator
{
    public function generateFromDocx(string $docxPath): string
    {
        if (! is_file($docxPath)) {
            throw new \RuntimeException('DOCX untuk konversi PDF tidak ditemukan.');
        }

        $workingDirectory = sys_get_temp_dir() . '/masjid_pdf_' . bin2hex(random_bytes(8));
        if (! mkdir($workingDirectory, 0700, true)) {
            throw new \RuntimeException('Tidak dapat membuat folder sementara PDF.');
        }

        $sourcePath = $workingDirectory . '/source.docx';
        $outputPath = $workingDirectory . '/source.pdf';
        try {
            if (! copy($docxPath, $sourcePath)) {
                throw new \RuntimeException('Tidak dapat menyalin DOCX untuk konversi PDF.');
            }

            $process = new Process(['soffice', '--headless', '--convert-to', 'pdf', '--outdir', $workingDirectory, $sourcePath]);
            $process->setTimeout(60);
            $process->run();
            if (! $process->isSuccessful() || ! is_file($outputPath)) {
                throw new \RuntimeException('Konversi DOCX ke PDF gagal: ' . trim($process->getErrorOutput()));
            }

            $temporaryPdf = tempnam(sys_get_temp_dir(), 'masjid_pdf_');
            if ($temporaryPdf === false || ! copy($outputPath, $temporaryPdf)) {
                throw new \RuntimeException('Tidak dapat menyimpan PDF sementara.');
            }

            return $temporaryPdf;
        } finally {
            @unlink($sourcePath);
            @unlink($outputPath);
            @rmdir($workingDirectory);
        }
    }
}
