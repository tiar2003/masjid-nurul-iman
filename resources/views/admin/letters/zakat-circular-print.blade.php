<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $letter->letter_number }} - Surat Edaran Zakat</title>
    <style>
        @page { size: legal portrait; margin: 10mm 20mm 15mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font: 11pt/1.13 'Times New Roman', serif; }
        .print-toolbar { display: flex; justify-content: flex-end; padding: 12px; font: 12px Arial, sans-serif; }
        .print-toolbar button { padding: 9px 14px; border: 0; background: #123d34; color: white; cursor: pointer; }
        .circular { width: 100%; }
        .letterhead { margin: 0 auto 18px; text-align: center; }
        .letterhead img { display: block; width: 100%; max-height: 34mm; object-fit: contain; }
        .letter-date { margin: 0 0 3px; text-align: right; }
        .letter-meta { margin-bottom: 14px; }
        .letter-meta p { margin: 0; }
        .letter-recipient { margin: 0 0 10px 62%; }
        .letter-recipient p { margin: 0; }
        .letter-body p { margin: 0 0 7px; text-align: justify; }
        .letter-body .item { margin: 0 0 3px 18px; text-align: left; }
        .letter-body .subitem { margin-left: 36px; }
        .signature-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 16px; text-align: center; }
        .signature-cell { position: relative; min-height: 115px; }
        .signature-cell--knowing { grid-column: 1 / -1; width: 42%; margin: 12px auto 0; }
        .signature-cell p { margin: 0; }
        .signature-space { height: 62px; }
        .stamp { position: absolute; top: 31px; left: 50%; width: 31mm; height: 31mm; object-fit: contain; opacity: .82; transform: translateX(-50%) rotate(-8deg); }
        .copy-list { margin-top: 10px; font-size: 10pt; }
        .draft-note { margin-top: 15px; color: #9a382c; font: 9pt Arial, sans-serif; }
        @media print { .print-toolbar, .draft-note { display: none !important; } }
        @media screen { body { max-width: 790px; margin: 18px auto; padding: 0 24px; } }
    </style>
</head>
<body>
    <div class="print-toolbar"><button type="button" onclick="window.print()">Cetak / Simpan PDF</button></div>
    @php($fields = $letter->field_data ?? [])
    <main class="circular">
        <header class="letterhead">
            <img src="{{ route('operations.reference-asset', 'kop-fix') }}" alt="Kop resmi Masjid Nurul Iman">
        </header>

        <p class="letter-date">Semarang, {{ $letter->issue_date->locale('id')->translatedFormat('d F Y') }}</p>
        <div class="letter-meta">
            <p>Nomor&nbsp; :&nbsp; {{ $letter->letter_number }}</p>
            <p>Lamp&nbsp;&nbsp;&nbsp; :&nbsp; 1 (satu) Lembar</p>
            <p>Hal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; Edaran Zakat Fitrah, Zakat Mal, Fidyah, Infaq, dan Shodaqoh</p>
        </div>

        <div class="letter-recipient">
            <p>Kepada&nbsp; Yth: {{ $fields['recipient'] }}</p>
            <p>Di Lingkungan RW IX</p>
        </div>

        <div class="letter-body">
            <p>Assalamualaikum Wr.Wb</p>
            <p>Teriring do’a semoga kita selalu tetap dalam lindungan Allah SWT. Sholawat dan Salam semoga tetap tercurahkan kepada Nabi Muhammad SAW. Al-Hamdulillah kita telah memasuki hari-hari terakhir dalam bulan suci Ramadhan {{ $fields['hijri_year'] }} H. Kami selaku Ta’mir Masjid Nurul Iman mengingatkan kepada Bapak, Ibu, Saudara Muslimin, Muslimat Di Lingkungan RW IX Kel. Krapyak untuk menyempurnakan Amal Ibadah Puasa kita dengan membayar Zakat.</p>
            <p>Sehubungan dengan hal tersebut, Ta’mir Masjid Nurul Iman akan menyelenggarakan pengumpulan dan penyaluran Zakat Fitrah, Zakat Mal, Fidyah, Infaq, dan Shadaqoh. Adapun rincian kegiatan tersebut adalah sebagai berikut:</p>
            <p class="item">1. Zakat Fitrah</p>
            <p class="item subitem">Berupa beras sebanyak {{ number_format((float) $fields['fitrah_kg_per_person'], 2, ',', '.') }} kg perjiwa.</p>
            <p class="item">2. Zakat Mal</p>
            <p class="item subitem">Berupa uang atau barang sejumlah {{ number_format((float) $fields['zakat_mal_rate_percent'], 2, ',', '.') }} % dari harta yang berhenti hasil perdagangan ( Profesi ) Selama 1 ( satu ) tahun yang telah sampai satu Nisab. ( keterangan lebih lanjut hubungi petugas)</p>
            <p class="item">3. Fidyah</p>
            <p class="item subitem">Sebagai pengganti puasa bagi yang tidak mampu menunaikan ibadah puasa karena sakit yang terus-menerus atau karena usia lanjut. Fidyah dapat diberikan berupa :</p>
            <p class="item subitem">a. Beras {{ number_format((float) $fields['fidyah_kg_per_day'], 2, ',', '.') }} kg untuk setiap hari puasa yang di tinggalkan atau</p>
            <p class="item subitem">b. Uang sebesar Rp. {{ number_format((float) $fields['fidyah_money_per_day'], 0, ',', '.') }},- setiap hari</p>
            <p class="item">4. Infaq dan Shadaqoh</p>
            <p class="item subitem">Dapat diberikan dalam bentuk barang maupun uang yang selanjutnya akan diserahkan kepada pihak yang berhak menerimanya.</p>
            <p class="item">5. Waktu pelaksanaan</p>
            <p class="item subitem">Penyerahan Zakat Fitrah, Zakat Mal, Fidyah, Infaq dan Shodaqoh dapat dilayani setiap hari pukul {{ $fields['service_hours'] }} (setelah shalat tarawih) sejak diterimanya surat edaran ini sampai dengan hari {{ \Carbon\Carbon::parse($fields['period_end'])->locale('id')->translatedFormat('l') }} tanggal {{ \Carbon\Carbon::parse($fields['period_end'])->locale('id')->translatedFormat('d F Y') }} ({{ $fields['closing_hijri_day'] }} {{ $fields['hijri_year'] }} H), bertempat di {{ $fields['venue'] }}.</p>
            <p class="item subitem">Demikian pemberitahuan kami, atas perhatian dan kepercayaan Bapak / Ibu / Sdr / I berikan, kami ucapkan terima kasih. Semoga Amal Ibadah Bapak / Ibu / Sdr / I diterima Allah SWT. Amin.</p>
            <p class="item subitem">Wassalamualaikum&nbsp; Wr.Wb</p>
        </div>

        <div class="signature-row">
            <div class="signature-cell"><p>Panitia Kegiatan Ramadhan</p><p>Masjid Nurul Iman</p><p>Ketua Takmir</p>@if ($fields['show_stamp'])<img class="stamp" src="{{ route('operations.reference-asset', 'cap-clean') }}" alt="Cap Masjid Nurul Iman">@endif<div class="signature-space"></div><strong>{{ $fields['chairperson_name'] }}</strong></div>
            <div class="signature-cell"><p>Sekertaris</p><div class="signature-space"></div><strong>{{ $fields['secretary_name'] ?? '' }}</strong></div>
            <div class="signature-cell signature-cell--knowing"><p>Mengetahui,</p><p>Ketua RW IX Kel. Krapyak</p><div class="signature-space"></div><strong>{{ $fields['knowing_official_name'] }}</strong></div>
        </div>

        <div class="copy-list">Tembusan:<br>{!! nl2br(e($fields['cc'])) !!}</div>
        <p class="draft-note">Draft · Periksa identitas/kop dan kecocokan hasil cetak terhadap template sumber sebelum menerbitkan.</p>
    </main>
</body>
</html><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $letter->letter_number }} - Surat Edaran Zakat</title>
    <style>
        @page { size: legal portrait; margin: 10mm 20mm 15mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font: 11pt/1.13 'Times New Roman', serif; }
        .print-toolbar { display: flex; justify-content: flex-end; padding: 12px; font: 12px Arial, sans-serif; }
        .print-toolbar button { padding: 9px 14px; border: 0; background: #123d34; color: white; cursor: pointer; }
        .circular { width: 100%; }
        .letterhead { margin: 0 auto 18px; text-align: center; }
        .letterhead img { width: 11mm; height: 11mm; display: block; margin: 0 auto 3px; object-fit: contain; }
        .letterhead p { margin: 0; font-weight: 700; line-height: 1.12; }
        .letterhead .secretariat { margin-top: 2px; font-size: 9.5pt; font-weight: 400; }
        .letter-date { margin: 0 0 3px; text-align: right; }
        .letter-meta { margin-bottom: 14px; }
        .letter-meta p { margin: 0; }
        .letter-recipient { margin: 0 0 10px 62%; }
        .letter-recipient p { margin: 0; }
        .letter-body p { margin: 0 0 7px; text-align: justify; }
        .letter-body .item { margin: 0 0 3px 18px; text-align: left; }
        .letter-body .subitem { margin-left: 36px; }
        .signature-row { display: grid; grid-template-columns: 1fr 1fr 1.2fr; gap: 10px; margin-top: 16px; text-align: center; }
        .signature-cell { position: relative; min-height: 115px; }
        .signature-cell p { margin: 0; }
        .signature-space { height: 62px; }
        .stamp { position: absolute; top: 31px; left: 50%; width: 31mm; height: 31mm; object-fit: contain; opacity: .82; transform: translateX(-50%) rotate(-8deg); }
        .copy-list { margin-top: 10px; font-size: 10pt; }
        .draft-note { margin-top: 15px; color: #9a382c; font: 9pt Arial, sans-serif; }
        @media print { .print-toolbar, .draft-note { display: none !important; } }
        @media screen { body { max-width: 790px; margin: 18px auto; padding: 0 24px; } }
    </style>
</head>
<body>
    <div class="print-toolbar"><button type="button" onclick="window.print()">Cetak / Simpan PDF</button></div>
    @php($fields = $letter->field_data ?? [])
    <main class="circular">
        <header class="letterhead">
            <img src="{{ route('operations.reference-asset', 'surat-edaran-mark') }}" alt="Lambang pada template asli">
            <p>TA’MIR MASJID NURUL IMAN</p>
            <p>RW. IX KELURAHAN KRAPYAK SEMARANG</p>
            <p class="secretariat">SEKERTARIAT : {{ $settings['secretariat_address'] ?? 'JL.HANOMAN IX NO.30 SEMARANG' }} TELP:{{ $settings['mosque_phone'] ?? '081325149999' }}</p>
        </header>

        <p class="letter-date">Semarang, {{ $letter->issue_date->locale('id')->translatedFormat('d F Y') }}</p>
        <div class="letter-meta">
            <p>Nomor&nbsp; :&nbsp; {{ $letter->letter_number }}</p>
            <p>Lamp&nbsp;&nbsp;&nbsp; :&nbsp; 1 (satu) Lembar</p>
            <p>Hal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; Edaran Zakat Fitrah, Zakat Mal, Fidyah, Infaq, dan Shodaqoh</p>
        </div>

        <div class="letter-recipient">
            <p>Kepada&nbsp; Yth: {{ $fields['recipient'] }}</p>
            <p>Di Lingkungan RW IX</p>
        </div>

        <div class="letter-body">
            <p>Assalamualaikum Wr.Wb</p>
            <p>Teriring do’a semoga kita selalu tetap dalam lindungan Allah SWT. Sholawat dan Salam semoga tetap tercurahkan kepada Nabi Muhammad SAW. Al-Hamdulillah kita telah memasuki hari-hari terakhir dalam bulan suci Ramadhan {{ $fields['hijri_year'] }} H. Kami selaku Ta’mir Masjid Nurul Iman mengingatkan kepada Bapak, Ibu, Saudara Muslimin, Muslimat Di Lingkungan RW IX Kel. Krapyak untuk menyempurnakan Amal Ibadah Puasa kita dengan membayar Zakat.</p>
            <p>Sehubungan dengan hal tersebut, Ta’mir Masjid Nurul Iman akan menyelenggarakan pengumpulan dan penyaluran Zakat Fitrah, Zakat Mal, Fidyah, Infaq, dan Shadaqoh. Adapun rincian kegiatan tersebut adalah sebagai berikut:</p>

            <p class="item">1. Zakat Fitrah</p>
            <p class="item subitem">Berupa beras sebanyak {{ number_format((float) $fields['fitrah_kg_per_person'], 2, ',', '.') }} kg perjiwa.</p>
            <p class="item">2. Zakat Mal</p>
            <p class="item subitem">Berupa uang atau barang sejumlah {{ number_format((float) $fields['zakat_mal_rate_percent'], 2, ',', '.') }} % dari harta yang berhenti hasil perdagangan ( Profesi ) selama 1 ( satu ) tahun yang telah sampai satu Nisab. ( keterangan lebih lanjut hubungi petugas)</p>
            <p class="item">3. Fidyah</p>
            <p class="item subitem">Sebagai pengganti puasa bagi yang tidak mampu menunaikan ibadah puasa karena sakit yang terus-menerus atau karena usia lanjut. Fidyah dapat diberikan berupa :</p>
            <p class="item subitem">a. Beras {{ number_format((float) $fields['fidyah_kg_per_day'], 2, ',', '.') }} kg untuk setiap hari puasa yang ditinggalkan atau</p>
            <p class="item subitem">b. Uang sebesar Rp {{ number_format((float) $fields['fidyah_money_per_day'], 0, ',', '.') }},- setiap hari</p>
            <p class="item">4. Infaq dan Shadaqoh</p>
            <p class="item subitem">Dapat diberikan dalam bentuk barang maupun uang yang selanjutnya akan diserahkan kepada pihak yang berhak menerimanya.</p>
            <p class="item">5. Waktu pelaksanaan</p>
            <p class="item subitem">Penyerahan Zakat Fitrah, Zakat Mal, Fidyah, Infaq dan Shodaqoh dapat dilayani setiap hari pukul {{ $fields['service_hours'] }} (setelah shalat tarawih) sejak diterimanya surat edaran ini sampai dengan hari {{ \Carbon\Carbon::parse($fields['period_end'])->locale('id')->translatedFormat('l') }} tanggal {{ \Carbon\Carbon::parse($fields['period_end'])->locale('id')->translatedFormat('d F Y') }} ({{ $fields['closing_hijri_day'] }} {{ $fields['hijri_year'] }} H), bertempat di {{ $fields['venue'] }}.</p>
            <p class="item subitem">Demikian pemberitahuan kami, atas perhatian dan kepercayaan Bapak / Ibu / Sdr / I berikan, kami ucapkan terima kasih. Semoga Amal Ibadah Bapak / Ibu / Sdr / I diterima Allah SWT. Amin.</p>
            <p class="item subitem">Wassalamualaikum&nbsp; Wr.Wb</p>
        </div>

        <div class="signature-row">
            <div class="signature-cell"><p>Panitia Kegiatan Ramadhan</p><p>Masjid Nurul Iman</p><p>Ketua</p><div class="signature-space"></div><strong>{{ $fields['chairperson_name'] }}</strong></div>
            <div class="signature-cell"><p>Sekertaris</p><div class="signature-space"></div><strong>{{ $fields['secretary_name'] ?? '' }}</strong></div>
            <div class="signature-cell"><p>Mengetahui,</p><p>Ketua Ta’mir Masjid Nurul Iman</p><p>RW IX Kel.Krapyak</p>@if ($fields['show_stamp'])<img class="stamp" src="{{ route('operations.reference-asset', 'cap-clean') }}" alt="Cap Masjid Nurul Iman">@endif<div class="signature-space"></div><strong>{{ $fields['knowing_official_name'] }}</strong></div>
        </div>

        <div class="copy-list">Tembusan:<br>{!! nl2br(e($fields['cc'])) !!}</div>
        <p class="draft-note">Draft · Periksa identitas/kop dan kecocokan hasil cetak terhadap template sumber sebelum menerbitkan.</p>
    </main>
</body>
</html>