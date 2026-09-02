<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Jadwal {{ $type }}</title>
    <style>
        /* Pengaturan Dasar */
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 20px 0;
            color: #000;
        }

        /* BUNGKUSAN KERTAS A4 */
        .kertas-a4 {
            width: 100%;
            max-width: 17.5cm;
            margin: 0 auto;
        }

        /* === KOP SURAT === */
        .kop-surat-image {
            width: 100%;
            margin-bottom: 15px;
            text-align: center;
        }
        .kop-surat-image img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* === JUDUL & TABEL === */
        .judul-jadwal {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 12px;
            border: 1px solid black;
        }

        th {
            border: 1px solid black;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
            background-color: transparent !important;
        }

        td {
            border-left: 1px solid black;
            border-right: 1px solid black;
            border-top: none;
            border-bottom: none;
            padding: 14px 6px;
            vertical-align: middle;
        }

        .baris-akhir-bulan td {
            border-bottom: 1px solid black;
        }

        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-uppercase { text-transform: uppercase; }

        /* === TANDA TANGAN & CATATAN === */
        .ttd-container {
            width: 100%;
            display: flex;
            justify-content: flex-end;
        }
        .ttd-box {
            text-align: center;
            width: 250px;
            font-size: 12px;
        }
        .ttd-box p {
            margin: 2px 0;
        }

        .catatan {
            margin-top: 15px;
            font-size: 11px;
            line-height: 1.3;
        }

        @media print {
            body { padding: 0; }
            @page { size: A4 portrait; margin: 15mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div style="text-align: right; margin-bottom: 10px;" class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">🖨️ Cetak Sekarang (CTRL+P)</button>
    </div>

    <!-- BUNGKUSAN KERTAS A4 -->
    <div class="kertas-a4">

        <!-- KOP SURAT GAMBAR -->
        <div class="kop-surat-image">
            <img src="{{ asset('KOP FIX.png') }}" alt="Kop Surat Masjid Nurul Iman">
        </div>

        @php
            \Carbon\Carbon::setLocale('id');

            $months = $schedules->map(function($s) {
                return \Carbon\Carbon::parse($s->date)->translatedFormat('F');
            })->unique();

            $monthNames = strtoupper($months->implode(', '));
            $year = \Carbon\Carbon::parse($schedules->first()->date ?? now())->format('Y');

            $groupedSchedules = $schedules->groupBy(function($item) {
                return \Carbon\Carbon::parse($item->date)->translatedFormat('F');
            });
        @endphp

        <!-- JUDUL -->
        <div class="judul-jadwal">
            JADWAL KHOTIB DAN IMAM MASJID NURUL IMAN<br>
            BULAN {{ $monthNames }} {{ $year }}
        </div>

        <!-- TABEL JADWAL -->
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">NO</th>
                    <th style="width: 50%;">KHOTIB DAN IMAM</th>
                    <th style="width: 25%;">TANGGAL</th>
                    <th style="width: 20%;">BULAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupedSchedules as $month => $items)
                    @foreach($items as $index => $s)
                    <tr class="{{ $loop->last ? 'baris-akhir-bulan' : '' }}">
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $s->speaker->name }}{{ $s->speaker->title ? ', '.$s->speaker->title : '' }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($s->date)->format('d-m-Y') }}</td>
                        @if($index == 0)
                            <td class="text-center text-bold text-uppercase" rowspan="{{ $items->count() }}" style="border-bottom: 1px solid black;">
                                {{ $month }}
                            </td>
                        @endif
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <!-- TANDA TANGAN -->
        <div class="ttd-container">
            <div class="ttd-box">
                <p>Semarang, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
                <p>Ketua Ta'mir Masjid Nurul Iman</p>

                <!-- Baris kosong untuk ruang tanda tangan basah -->
                <br><br><br>

                <p style="font-weight: bold; text-decoration: underline;">H. Sugeng Tiyarto, SH. MH</p>
            </div>
        </div>

        <!-- CATATAN -->
        <div class="catatan">
            <p>Catatan:<br>
            Apabila khotib berhalangan harap memberitahu pengurus Masjid Nurul Iman pada hari rabu <br>
            (dua hari sebelum hari jum’at). NO. HP: 082242271594 (Bp. Yatardin)</p>
        </div>

    </div> <!-- Akhir bungkusan kertas -->

</body>
</html>
