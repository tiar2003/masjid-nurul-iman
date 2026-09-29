<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hasil Akhir Zakat {{ $periodSnapshot['year'] }}</title>
    <style>
        @page { size: legal portrait; margin: 42.51mm 25.4mm 25.4mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font: 11pt/1.12 'Times New Roman', serif; }
        .print-toolbar { display: flex; justify-content: flex-end; padding: 14px; font: 12px Arial, sans-serif; }
        .print-toolbar button { padding: 9px 14px; border: 0; background: #123d34; color: white; cursor: pointer; }
        .report { max-width: 100%; }
        .report-title { margin: 0 0 2px; text-align: center; font-size: 12pt; font-weight: 700; }
        .report-subtitle { margin: 0 0 20px; text-align: center; font-size: 12pt; font-weight: 700; }
        .report p { margin: 0 0 7px; }
        .report h2 { margin: 10px 0 5px; font-size: 11pt; font-weight: 400; }
        .report-line { display: grid; grid-template-columns: 116px 18px 1fr; margin-left: 17px !important; }
        .report-nested { margin-left: 35px !important; }
        .report-section { margin-top: 13px !important; font-weight: 400; }
        .report-asnaf { margin-left: 24px !important; }
        .report-total { margin-left: 25px !important; }
        .report-note { margin-left: 24px !important; font-style: normal; }
        .report-warning { margin-top: 14px; padding: 10px; border: 1px solid #ba5b43; color: #783a2c; font: 10pt Arial, sans-serif; }
        @media print { .print-toolbar, .report-warning { display: none !important; } }
        @media screen { body { max-width: 850px; margin: 18px auto; padding: 0 24px; } }
    </style>
</head>
<body>
    <div class="print-toolbar"><button type="button" onclick="window.print()">Cetak / Simpan PDF</button></div>
    <main class="report">
        <p class="report-title">PANITIA ZAKAT MASJID NURUL IMAN {{ $periodSnapshot['hijri_year'] }} / {{ $periodSnapshot['year'] }}</p>
        <p class="report-subtitle">TELAH MENERIMA ZAKAT FITRAH, ZAKAT MAL, FIDYAH, DAN SHODAQOH</p>

        <p>1. Zakat Fitrah Berupa:</p>
        <p class="report-line"><span>➢ Beras</span><span>:</span><span>{{ number_format($summary['receipts']['fitrah_rice_kg'], 2, ',', '.') }} Kg</span></p>
        <p class="report-line"><span>➢ Uang</span><span>:</span><span>Rp {{ number_format($summary['receipts']['fitrah_money'], 0, ',', '.') }}</span></p>
        <p class="report-line"><span>Untuk</span><span>:</span><span>{{ number_format($summary['receipts']['souls'], 0, ',', '.') }} Jiwa</span></p>
        <p>2. Zakat Mal: Rp {{ number_format($summary['receipts']['zakat_mal'], 0, ',', '.') }}</p>
        <p>3. Fidyah: {{ $summary['receipts']['fidyah_money'] > 0 ? 'Rp ' . number_format($summary['receipts']['fidyah_money'], 0, ',', '.') : '-' }}{{ $summary['receipts']['fidyah_rice_kg'] > 0 ? ' dan ' . number_format($summary['receipts']['fidyah_rice_kg'], 2, ',', '.') . ' Kg beras' : '' }}</p>
        <p>4. Shodaqoh: Rp {{ number_format($summary['receipts']['shodaqoh'], 0, ',', '.') }}</p>

        <p style="margin-top:12px">Al-Hamdullilah, telah kami Salurkan kepada yang berhak menerima Mulai tgl
            {{ !empty($periodSnapshot['starts_at']) ? \Carbon\Carbon::parse($periodSnapshot['starts_at'])->locale('id')->translatedFormat('d F Y') : '-' }} s/d
            {{ !empty($periodSnapshot['ends_at']) ? \Carbon\Carbon::parse($periodSnapshot['ends_at'])->locale('id')->translatedFormat('d F Y') : '-' }} / {{ $periodSnapshot['hijri_year'] }} H diantaranya:
        </p>
        <p class="report-asnaf">1. Fakir Miskin</p>
        <p class="report-asnaf">2. Panti Asuhan</p>
        <p class="report-asnaf">3. Musholla</p>

        <p class="report-section">Pendistribusian:</p>
        <p>1. Zakat Fitrah Berupa:</p>
        <p class="report-line"><span>Beras</span><span>:</span><span>{{ number_format($summary['distributions']['fitrah_rice_kg'], 2, ',', '.') }} Kg</span></p>
        <p class="report-line"><span>Uang</span><span>:</span><span>Rp {{ number_format($summary['distributions']['fitrah_money'], 0, ',', '.') }}</span></p>
        <p>4. Zakat Mal: Rp {{ number_format($summary['distributions']['zakat_mal'], 0, ',', '.') }}</p>
        <p class="report-nested">5. Fidyah: Rp {{ number_format($summary['distributions']['fidyah_money'], 0, ',', '.') }}</p>
        <p class="report-total">Jumlah: Rp {{ number_format($summary['distributions']['fitrah_money'] + $summary['distributions']['zakat_mal'] + $summary['distributions']['fidyah_money'], 0, ',', '.') }}</p>
        <p class="report-note">(Disalurkan kepada pihak yang berhak menerima)</p>
        <p class="report-nested">6. Shodaqoh: Rp {{ number_format($summary['distributions']['shodaqoh'], 0, ',', '.') }}</p>
        <p class="report-note">(Masuk ke kas Masjid Nurul Iman)</p>

        @if ($summary['remaining']['fitrah_rice_kg'] < 0 || $summary['remaining']['fitrah_money'] < 0 || $summary['remaining']['zakat_mal'] < 0 || $summary['remaining']['fidyah_money'] < 0)
            <div class="report-warning">PERIKSA: jumlah penyaluran melebihi penerimaan pada salah satu kategori. Laporan ini perlu ditinjau sebelum digunakan.</div>
        @endif
        <div class="report-warning">Draft otomatis · Dibuat {{ now()->locale('id')->translatedFormat('d F Y H:i') }} · {{ $report->id }}</div>
    </main>
</body>
</html>