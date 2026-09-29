<?php

namespace App\Services;

use App\Models\OfficialSchedule;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use ZipArchive;

class OfficialScheduleDocxImporter
{
    public function import(string $path, bool $alignMonth): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new InvalidArgumentException('File DOCX tidak dapat dibuka.');
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) {
            throw new InvalidArgumentException('Isi tabel Word tidak ditemukan.');
        }

        $previousErrorSetting = libxml_use_internal_errors(true);
        $document = new DOMDocument();
        $loaded = $document->loadXML($xml, LIBXML_NONET | LIBXML_NOBLANKS);
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorSetting);

        if (!$loaded) {
            throw new InvalidArgumentException('Struktur dokumen Word tidak valid.');
        }

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $rows = [];
        $correctedMonthCount = 0;
        $mismatchedDates = [];

        foreach ($xpath->query('//w:tbl/w:tr') as $tableRow) {
            $cells = $xpath->query('./w:tc', $tableRow);
            if ($cells->length < 4) {
                continue;
            }

            $monthLines = $this->cellLines($xpath, $cells->item(3));
            $monthLabel = strtoupper(implode(' ', $monthLines));
            $tableMonth = $this->monthNumber($monthLabel);
            if ($tableMonth === null) {
                continue;
            }

            $names = $this->cellLines($xpath, $cells->item(1));
            $dateLines = $this->cellLines($xpath, $cells->item(2));
            $dates = [];
            foreach ($dateLines as $dateLine) {
                if (preg_match_all('/\b\d{2}-\d{2}-\d{4}\b/', $dateLine, $matches)) {
                    array_push($dates, ...$matches[0]);
                }
            }

            if (count($names) !== count($dates)) {
                throw new InvalidArgumentException("Jumlah nama dan tanggal pada bagian {$monthLabel} tidak sama.");
            }

            foreach ($dates as $index => $dateText) {
                [$day, $sourceMonth, $year] = array_map('intval', explode('-', $dateText));
                if (!checkdate($sourceMonth, $day, $year)) {
                    throw new InvalidArgumentException("Tanggal {$dateText} pada dokumen tidak valid.");
                }

                if ($sourceMonth !== $tableMonth) {
                    $mismatchedDates[] = "{$dateText} pada bagian {$monthLabel}";
                    if ($alignMonth) {
                        $correctedMonthCount++;
                    }
                }

                $targetMonth = $alignMonth ? $tableMonth : $sourceMonth;
                if (!checkdate($targetMonth, $day, $year)) {
                    throw new InvalidArgumentException("Tanggal {$day} tidak valid untuk bagian {$monthLabel}.");
                }

                $date = sprintf('%04d-%02d-%02d', $year, $targetMonth, $day);
                $name = trim(preg_replace('/\s+/', ' ', $names[$index]) ?? $names[$index]);
                if ($name === '') {
                    throw new InvalidArgumentException("Nama penceramah untuk tanggal {$dateText} kosong.");
                }
                if (isset($rows[$date])) {
                    throw new InvalidArgumentException("Tanggal {$date} tercantum lebih dari satu kali.");
                }

                $rows[$date] = [
                    'date' => $date,
                    'speaker_name' => $name,
                ];
            }
        }

        if ($rows === []) {
            throw new InvalidArgumentException('Tidak ada jadwal yang dapat dibaca dari tabel DOCX.');
        }

        if ($mismatchedDates !== [] && !$alignMonth) {
            throw new InvalidArgumentException(
                'Bulan pada beberapa tanggal berbeda dari kolom BULAN: ' . implode(', ', $mismatchedDates) .
                '. Periksa dokumen atau pilih opsi penyelarasan bulan sebelum impor.'
            );
        }

        $scheduleRows = array_values($rows);
        DB::transaction(function () use ($scheduleRows) {
            $months = array_unique(array_map(fn ($row) => substr($row['date'], 0, 7), $scheduleRows));
            foreach ($months as $month) {
                $start = $month . '-01';
                OfficialSchedule::whereBetween('date', [$start, date('Y-m-t', strtotime($start))])->delete();
            }

            foreach ($scheduleRows as $scheduleRow) {
                OfficialSchedule::create($scheduleRow);
            }
        });

        return [
            'count' => count($scheduleRows),
            'corrected_month_count' => $correctedMonthCount,
        ];
    }

    private function cellLines(DOMXPath $xpath, \DOMElement $cell): array
    {
        $lines = [];
        foreach ($xpath->query('./w:p', $cell) as $paragraph) {
            $text = '';
            foreach ($xpath->query('.//w:t', $paragraph) as $textNode) {
                $text .= $textNode->textContent;
            }

            $text = trim($text);
            if ($text !== '') {
                $lines[] = $text;
            }
        }

        return $lines;
    }

    private function monthNumber(string $monthLabel): ?int
    {
        foreach ([
            'OKTOBER' => 10,
            'NOVEMBER' => 11,
            'DESEMBER' => 12,
        ] as $name => $number) {
            if (str_contains($monthLabel, $name)) {
                return $number;
            }
        }

        return null;
    }
}