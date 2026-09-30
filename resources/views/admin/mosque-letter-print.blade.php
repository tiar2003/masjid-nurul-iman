<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $letter->letter_number }} - {{ $letter->subject }}</title>
    <style>
        @page { size: A4; margin: 22mm 24mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #17211c; font: 12pt/1.65 Georgia, 'Times New Roman', serif; }
        .print-actions { display: flex; justify-content: flex-end; padding: 16px; font: 12px Arial, sans-serif; }
        .print-actions button { padding: 10px 14px; border: 0; background: #123d34; color: white; cursor: pointer; }
        .letter { max-width: 760px; margin: 0 auto; }
        .letter-head { margin: 0 0 18px; text-align: center; }
        .letter-head img { display: block; width: 100%; height: auto; max-height: 34mm; object-fit: contain; }
        .letter-meta { margin: 24px 0 28px; }
        .letter-meta p { margin: 2px 0; }
        .letter-subject { margin: 18px 0 24px; text-align: center; }
        .letter-subject strong { text-decoration: underline; }
        .letter-body { min-height: 260px; white-space: normal; }
        .letter-body p { margin: 0 0 11px; }
        .letter-signature { width: 245px; margin: 28px 0 0 auto; text-align: center; }
        .letter-signature__space { height: 72px; }
        @media print { .print-actions { display: none; } }
        @media (max-width: 600px) { body { padding: 12px; font-size: 11pt; } }
    </style>
</head>
<body>
    <div class="print-actions"><button type="button" onclick="window.print()">Cetak / Simpan PDF</button></div>
    <main class="letter">
        <header class="letter-head">
            <img src="{{ route('operations.reference-asset', 'kop-fix') }}" alt="Kop resmi Masjid Nurul Iman">
        </header>
        <div class="letter-meta">
            <p>Nomor: {{ $letter->letter_number }}</p>
            <p>Perihal: {{ $letter->subject }}</p>
            <p>Lampiran: -</p>
        </div>
        <p>Kepada Yth.<br>{{ $letter->recipient }}<br>di tempat</p>
        <div class="letter-subject"><strong>{{ $letter->subject }}</strong></div>
        <p>Assalamu'alaikum warahmatullahi wabarakatuh,</p>
        <div class="letter-body">{!! nl2br(e($letter->body)) !!}</div>
        <p>Demikian surat ini kami sampaikan. Atas perhatian dan kerja samanya kami ucapkan terima kasih.</p>
        <p>Wassalamu'alaikum warahmatullahi wabarakatuh.</p>
        <div class="letter-signature">
            <p>Semarang, {{ $letter->issue_date->locale('id')->translatedFormat('d F Y') }}<br>Pengurus Masjid Nurul Iman</p>
            <div class="letter-signature__space"></div>
            <strong>( Ketua Takmir )</strong>
        </div>
    </main>
</body>
</html>