<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Kultum Shalat Tarawih - Masjid Nurul Iman</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;
            background: #fff;
            color: #000;
            font-family: "Times New Roman", Times, serif;
            font-size: 12px;
            font-weight: bold;
        }

        .sheet {
            position: relative;
            width: 210mm;
            height: 297mm;
            overflow: hidden;
            background: #fff;
        }

        /* Garis luar hitam tipis */
        .frame-outer {
            position: absolute;
            left: 8.1mm;
            top: 8.1mm;
            right: 8.1mm;
            bottom: 8.1mm;
            border: 0.45mm solid #000;
            z-index: 1;
        }

        /* Garis biru tebal */
        .frame-inner {
            position: absolute;
            left: 9.2mm;
            top: 9.2mm;
            right: 9.2mm;
            bottom: 9.2mm;
            border: 1.15mm solid #11118b;
            z-index: 1;
        }

        /* Garis hitam tipis di luar garis biru */
        .frame-black-inner {
            position: absolute;
            left: 8.3mm;
            top: 8.3mm;
            right: 8.3mm;
            bottom: 8.3mm;
            border: 0.25mm solid #000;
            z-index: 2;
            pointer-events: none;
        }

        /* HEADER */
        .header {
            position: absolute;
            left: 20mm;
            top: 13.5mm;
            width: 170mm;
            height: 35mm;
            z-index: 10;
        }

        /* Logo Diperbesar Jauh Lebih Jelas dan Proporsional */
        .logo {
            position: absolute;
            left: 0;
            top: -3mm;
            width: 55mm; 
            height: auto;
            object-fit: contain;
        }

        /* Teks Kop Disejajarkan di Samping Kanan Logo */
        .header-text {
            position: absolute;
            left: 55mm; 
            top: 4.5mm;
            width: 115mm;
            text-align: center;
            white-space: nowrap;
        }

        .header-text .line-1 {
            margin: 0;
            font-size: 15px;
            line-height: 1.12;
            font-weight: 700;
        }

        .header-text .line-2 {
            margin: 1.2mm 0 0;
            font-size: 14px;
            line-height: 1.12;
            font-weight: 700;
        }

        .header-text .line-3 {
            margin: 1.2mm 0 0;
            font-size: 14px;
            line-height: 1.12;
            font-weight: 700;
        }

        /* TABEL */
        .schedule-wrap {
            position: absolute;
            left: 29.6mm;
            top: 47.5mm;
            width: 153.0mm;
            z-index: 10;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 12px;
            font-weight: bold;
        }

        .schedule-table th,
        .schedule-table td {
            border: 0.32mm solid #000;
            padding: 0;
            vertical-align: middle;
        }

        .schedule-table thead th {
            height: 5.5mm;
            font-size: 12px;
            line-height: 1;
            text-align: center;
            font-weight: 700;
            white-space: nowrap;
        }

        .schedule-table tbody td {
            height: 5.8mm;
            line-height: 1;
            font-size: 12px;
            font-weight: 700;
        }

        .col-no { width: 11.0mm; }
        .col-hari { width: 29.0mm; }
        .col-tanggal { width: 33.5mm; }
        .col-penceramah { width: auto; }

        .cell-center {
            text-align: center;
        }

        .cell-speaker {
            text-align: left;
            padding-left: 2.0mm !important;
            padding-right: 1.0mm !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* KETERANGAN */
        .notes {
            position: absolute;
            left: 25.3mm;
            top: 233mm;
            width: 157mm;
            z-index: 10;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.25;
        }

        .notes-title {
            margin: 0 0 0.8mm 0;
            font-weight: 700;
        }

        .notes-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .notes-list div {
            margin: 0;
            padding: 0;
        }

        .no-print {
            font-family: Arial, sans-serif;
        }

        @media print {
            html,
            body {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 0;
                background: #fff;
            }

            .no-print {
                display: none !important;
            }

            .sheet {
                width: 210mm;
                height: 297mm;
                page-break-after: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <!-- Tombol cetak untuk browser -->
    <div class="no-print" style="position: fixed; top: 0; left: 0; right: 0; z-index: 1000; padding: 10px; background: #f8fafc; border-bottom: 1px solid #ddd; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; border: 0; border-radius: 5px; background: #059669; color: #fff; font-weight: 700; cursor: pointer;">
            Cetak Sekarang (CTRL+P)
        </button>
    </div>

    <div class="sheet">

        <!-- FRAME -->
        <div class="frame-outer"></div>
        <div class="frame-black-inner"></div>
        <div class="frame-inner"></div>

        <!-- HEADER (Logo Besar Sesuai Permintaan) -->
        <div class="header">
            <img class="logo" src="{{ asset('logo-masjid.png') }}" alt="Logo Masjid Nurul Iman">

            <div class="header-text">
                <div class="line-1">JADWAL KULTUM SHALAT TARAWIH</div>
                <div class="line-2">MASJID NURUL IMAN SEMARANG</div>
                <div class="line-3">RAMADHAN 1447 H/2026 M</div>
            </div>
        </div>

        <!-- TABEL -->
        <div class="schedule-wrap">
            <table class="schedule-table">
                <colgroup>
                    <col class="col-no">
                    <col class="col-hari">
                    <col class="col-tanggal">
                    <col class="col-penceramah">
                </colgroup>

                <thead>
                    <tr>
                        <th>NO</th>
                        <th>HARI</th>
                        <th>TANGGAL</th>
                        <th>PENCERAMAH</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($schedules as $index => $schedule)

                        @php
                            $daysMap = [
                                'Sunday'    => 'AHAD',
                                'Monday'    => 'SENIN',
                                'Tuesday'   => 'SELASA',
                                'Wednesday' => 'RABU',
                                'Thursday'  => 'KAMIS',
                                'Friday'    => 'JUMAT',
                                'Saturday'  => 'SABTU',
                            ];

                            $date = \Carbon\Carbon::parse($schedule->date);
                            $hari = $daysMap[$date->format('l')] ?? $date->format('l');

                            $speaker = $schedule->speaker
                                ? $schedule->speaker->name
                                    . ($schedule->speaker->title
                                        ? ', ' . $schedule->speaker->title
                                        : '')
                                : '-';
                        @endphp

                        <tr>
                            <td class="cell-center">{{ $index + 1 }}</td>
                            <td class="cell-center">{{ $hari }}</td>
                            <td class="cell-center">{{ $date->format('d/m/Y') }}</td>
                            <td class="cell-speaker">{{ $speaker }}</td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="4" class="cell-center" style="height: 10mm;">
                                Belum ada data jadwal kultum.
                            </td>
                        </tr>

                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- KETERANGAN -->
        <div class="notes">
            <div class="notes-title">Keterangan:</div>
            <div class="notes-list">
                <div>1. Materi kultum diserahkan kepada penceramah masing-masing.</div>
                <div>2. Shalat Tarawih 8 raka’at dan shalat witir 3 raka’at ( 2 salam/ 1 salam ).</div>
                <div>3. Kultum dilaksanakan setelah shalat tarawih sebelum shalat witir.</div>
                <div>4. Apabila berhalangan hadir, harap memberitahukan sebelumnya kepada Ta’mir Masjid Nurul Iman.</div>
            </div>
        </div>

    </div>

</body>
</html>