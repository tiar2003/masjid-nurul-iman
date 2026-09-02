<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kotak Amal Terawih 2025</title>
<link rel="stylesheet" href="{{ asset('amal/style.css') }}">
</head>

<body>

<div class="container">

    <div class="kop">
       <img src="{{ asset('amal/KOP FIX.png') }}" alt="Kop Masjid">
    </div>

    <h2>HASIL KOTAK AMAL SOLAT TERAWIH 1447 H / 2026 M</h2>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Hari</th>
                    <th>Tanggal</th>
                    <th>Hasil Kotak Amal (Rp)</th>
                    <th>Jumlah </th>
                </tr>
            </thead>
            <tbody id="dataTable"></tbody>
        </table>
    </div>

    <div class="total-box">
        Total Keseluruhan: Rp <span id="grandTotal">0</span>
    </div>

<div class="action-buttons">

    <div class="top-buttons">
        <button onclick="printTable()" class="print-btn">🖨 Print</button>
        <button onclick="exportExcel()" class="excel-btn">📊 Export Excel</button>
    </div>

    <button onclick="resetData()" class="reset-btn">Reset Semua Data</button>

</div>

<script src="{{ asset('amal/script.js') }}"></script>
</body>
</html>
