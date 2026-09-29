<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tanda Penyerahan dan Tanda Terima Zakat</title>
    <style>
        @page { size: 330mm 215.9mm; margin: 10mm 10mm 7.5mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font: 9pt/1.08 'Times New Roman', serif; }
        .print-toolbar { display: flex; justify-content: flex-end; gap: 8px; padding: 12px; font: 12px Arial, sans-serif; }
        .print-toolbar a, .print-toolbar button { padding: 9px 12px; border: 0; background: #123d34; color: white; text-decoration: none; cursor: pointer; }
        .copies { display: grid; grid-template-columns: 1fr 1fr; gap: 8mm; }
        .copy { position: relative; min-width: 0; padding: 1mm 2mm; }
        .copy-head { position: relative; min-height: 22mm; padding-left: 14mm; text-align: center; }
        .copy-head img { position: absolute; left: 0; top: 0; width: 11mm; height: 11mm; object-fit: contain; }
        .copy-head p { margin: 0; font-weight: 700; line-height: 1.1; }
        .copy-head .address { margin-top: 1mm; font-size: 8pt; font-weight: 400; }
        .copy-title { margin: 4mm 0 3mm; text-align: center; font-size: 10pt; font-weight: 700; letter-spacing: .2px; }
        .copy p { margin: 0 0 2mm; }
        .copy .item { margin-left: 4mm; }
        .copy .subitem { margin-left: 10mm; }
        .copy .person { margin: 1mm 0 2mm 11mm; }
        .sign { width: 44%; min-height: 29mm; margin: 2mm 0 0 auto; position: relative; text-align: center; }
        .sign-space { height: 15mm; }
        .stamp { position: absolute; top: 6mm; left: 50%; width: 26mm; height: 26mm; object-fit: contain; opacity: .8; transform: translateX(-50%) rotate(-8deg); }
        @media print { .print-toolbar { display: none; } }
        @media screen { body { max-width: 1250px; margin: 16px auto; padding: 0 16px; } }
        @media (max-width: 700px) { .copies { grid-template-columns: 1fr; } .copy { padding-bottom: 10mm; border-bottom: 1px dashed #aaa; } }
    </style>
</head>
<body>
    <div class="print-toolbar">
        <a href="{{ route('zakat-admin.receipts.print', ['receipt' => $receipt->id, 'stamp' => 0]) }}">Tanpa cap</a>
        <a href="{{ route('zakat-admin.receipts.print', ['receipt' => $receipt->id, 'stamp' => 1]) }}">Dengan cap</a>
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>
    @foreach ([['title' => 'TANDA PENYERAHAN', 'lead' => 'Pada saat ini kami serahkan :', 'sign' => 'Yang menyerahkan', 'person' => $receipt->muzakki_name], ['title' => 'TANDA TERIMA', 'lead' => 'Pada saat ini kami terima :', 'sign' => 'Yang menerima', 'person' => $official ?: ($settings['mosque_name'] ?? 'TA’MIR MASJID NURUL IMAN')]] as $copy)
        <main class="copy">
            <header class="copy-head">
                <img src="{{ route('operations.reference-asset', 'tanda-zakat-mark') }}" alt="Lambang pada template asli">
                <p>TA’MIR MASJID NURUL&nbsp; IMAN</p>
                <p>RW. IX KELURAHAN KRAPYAK SEMARANG</p>
                <p class="address">SEKRETARIAT : {{ $settings['secretariat_address'] ?? 'JL.HANOMAN IX NO.30 SEMARANG' }} TELP.{{ $settings['mosque_phone'] ?? '081325149999' }}</p>
            </header>
            <h1 class="copy-title">{{ $copy['title'] }}</h1>
            <p>Assalamualaikum Wr.Wb</p>
            <p>{{ $copy['lead'] }}</p>
            <p class="item">1. Zakat Fitrah berupa - Beras : {{ number_format($receipt->fitrah_rice_kg, 2, ',', '.') }} kg Untuk : {{ $receipt->souls }} jiwa</p>
            <p class="item">- Uang : Rp{{ number_format($receipt->fitrah_money, 0, ',', '.') }} Untuk : {{ $receipt->souls }} jiwa</p>
            <p class="person">( {{ $receipt->muzakki_name }} )</p>
            <p class="item">2. Zakat Mal : 1. Rp{{ number_format($receipt->zakat_mal, 0, ',', '.') }}</p>
            <p class="item subitem">2. ................................................</p>
            <p class="item">3. Fidyah berupa - Beras : {{ number_format($receipt->fidyah_rice_kg, 2, ',', '.') }} kg Untuk : {{ $receipt->fidyah_days }} hari</p>
            <p class="item">- Uang : Rp{{ number_format($receipt->fidyah_money, 0, ',', '.') }} Untuk : {{ $receipt->fidyah_days }} hari</p>
            <p class="person">( {{ $receipt->muzakki_name }} )</p>
            <p class="item">4. Infaq dan Shodaqoh&nbsp; 1. Rp{{ number_format($receipt->infaq, 0, ',', '.') }}</p>
            <p class="item subitem">2. Rp{{ number_format($receipt->shodaqoh, 0, ',', '.') }}</p>
            <p class="person">( {{ $receipt->muzakki_name }} )</p>
            <p>Mohon diserahkan / disampaikan kepada yang berhak menerima sesuai dengan ketentuan dalam Agama Islam. Terima kasih.</p>
            <p>Wassalamualaikum&nbsp; Wr.Wb</p>
            <div class="sign">
                <p>Semarang, {{ $receipt->date->locale('id')->translatedFormat('d F Y') }}</p>
                <p>{{ $copy['sign'] }}</p>
                @if ($showStamp)<img class="stamp" src="{{ route('operations.reference-asset', 'cap-clean') }}" alt="Cap Masjid Nurul Iman">@endif
                <div class="sign-space"></div>
                <strong>( {{ $copy['person'] }} )</strong>
            </div>
        </main>
    @endforeach
</body>
</html><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tanda Penyerahan dan Tanda Terima Zakat</title>
    <style>
        @page { size: 330mm 215.9mm; margin: 10mm 10mm 7.5mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font: 9pt/1.08 'Times New Roman', serif; }
        .print-toolbar { display: flex; justify-content: flex-end; gap: 8px; padding: 12px; font: 12px Arial, sans-serif; }
        .print-toolbar a, .print-toolbar button { padding: 9px 12px; border: 0; background: #123d34; color: white; text-decoration: none; cursor: pointer; }
        .copies { display: grid; grid-template-columns: 1fr 1fr; gap: 8mm; }
        .copy { position: relative; min-width: 0; padding: 1mm 2mm; }
        .copy-head { position: relative; min-height: 22mm; padding-left: 14mm; text-align: center; }
        .copy-head img { position: absolute; left: 0; top: 0; width: 11mm; height: 11mm; object-fit: contain; }
        .copy-head p { margin: 0; font-weight: 700; line-height: 1.1; }
        .copy-head .address { margin-top: 1mm; font-size: 8pt; font-weight: 400; }
        .copy-title { margin: 4mm 0 3mm; text-align: center; font-size: 10pt; font-weight: 700; letter-spacing: .2px; }
        .copy p { margin: 0 0 2mm; }
        .copy .item { margin-left: 4mm; }
        .copy .subitem { margin-left: 10mm; }
        .copy .person { margin: 1mm 0 2mm 11mm; }
        .sign { width: 44%; min-height: 29mm; margin: 2mm 0 0 auto; position: relative; text-align: center; }
        .sign-space { height: 15mm; }
        .stamp { position: absolute; top: 6mm; left: 50%; width: 26mm; height: 26mm; object-fit: contain; opacity: .8; transform: translateX(-50%) rotate(-8deg); }
        @media print { .print-toolbar { display: none; } }
        @media screen { body { max-width: 1250px; margin: 16px auto; padding: 0 16px; } }
        @media (max-width: 700px) { .copies { grid-template-columns: 1fr; } .copy { padding-bottom: 10mm; border-bottom: 1px dashed #aaa; } }
    </style>
</head>
<body>
    <div class="print-toolbar">
        <a href="{{ route('zakat-admin.receipts.print', ['receipt' => $receipt->id, 'stamp' => 0]) }}">Tanpa cap</a>
        <a href="{{ route('zakat-admin.receipts.print', ['receipt' => $receipt->id, 'stamp' => 1]) }}">Dengan cap</a>
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>
    @foreach ([['title' => 'TANDA PENYERAHAN', 'lead' => 'Pada saat ini kami serahkan :', 'sign' => 'Yang menyerahkan', 'person' => $receipt->muzakki_name], ['title' => 'TANDA TERIMA', 'lead' => 'Pada saat ini kami terima :', 'sign' => 'Yang menerima', 'person' => $official ?: ($settings['mosque_name'] ?? 'TA’MIR MASJID NURUL IMAN')]] as $copy)
        <main class="copy">
            <header class="copy-head">
                <img src="{{ route('operations.reference-asset', 'tanda-zakat-mark') }}" alt="Lambang pada template asli">
                <p>TA’MIR MASJID NURUL&nbsp; IMAN</p>
                <p>RW. IX KELURAHAN KRAPYAK SEMARANG</p>
                <p class="address">SEKRETARIAT : {{ $settings['secretariat_address'] ?? 'JL.HANOMAN IX NO.30 SEMARANG' }} TELP.{{ $settings['mosque_phone'] ?? '081325149999' }}</p>
            </header>
            <h1 class="copy-title">{{ $copy['title'] }}</h1>
            <p>Assalamualaikum Wr.Wb</p>
            <p>{{ $copy['lead'] }}</p>
            <p class="item">1. Zakat Fitrah berupa - Beras : {{ number_format($receipt->fitrah_rice_kg, 2, ',', '.') }} kg Untuk : {{ $receipt->souls }} jiwa</p>
            <p class="item">- Uang : Rp {{ number_format($receipt->fitrah_money, 0, ',', '.') }} Untuk : {{ $receipt->souls }} jiwa</p>
            <p class="person">( {{ $receipt->muzakki_name }} )</p>
            <p class="item">2. Zakat Mal : 1. Rp {{ number_format($receipt->zakat_mal, 0, ',', '.') }}</p>
            <p class="item subitem">2. ................................................</p>
            <p class="item">3. Fidyah berupa - Beras : {{ number_format($receipt->fidyah_rice_kg, 2, ',', '.') }} kg Untuk : {{ $receipt->fidyah_days }} hari</p>
            <p class="item">- Uang : Rp {{ number_format($receipt->fidyah_money, 0, ',', '.') }} Untuk : {{ $receipt->fidyah_days }} hari</p>
            <p class="person">( {{ $receipt->muzakki_name }} )</p>
            <p class="item">4. Infaq dan Shodaqoh&nbsp; 1. Rp {{ number_format($receipt->infaq, 0, ',', '.') }}</p>
            <p class="item subitem">2. Rp {{ number_format($receipt->shodaqoh, 0, ',', '.') }}</p>
            <p class="person">( {{ $receipt->muzakki_name }} )</p>
            <p>Mohon diserahkan / disampaikan kepada yang berhak menerima sesuai dengan ketentuan dalam Agama Islam. Terima kasih.</p>
            <p>Wassalamualaikum&nbsp; Wr.Wb</p>
            <div class="sign">
                <p>Semarang, {{ $receipt->date->locale('id')->translatedFormat('d F Y') }}</p>
                <p>{{ $copy['sign'] }}</p>
                @if ($showStamp)<img class="stamp" src="{{ route('operations.reference-asset', 'cap-clean') }}" alt="Cap Masjid Nurul Iman">@endif
                <div class="sign-space"></div>
                <strong>( {{ $copy['person'] }} )</strong>
            </div>
        </main>
    @endforeach
</body>
</html>