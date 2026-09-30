# Audit Website & Administrasi Masjid Nurul Iman

## 1) Status audit

- Project Laravel aktif dan sudah memiliki modul operasional yang cukup maju untuk pengelolaan masjid.
- Stack yang terdeteksi: Laravel 9, PHP 8.0+, Blade, Tailwind CSS, Breeze auth, routes terstruktur, database migrations, model Eloquent, dan UI admin yang sudah dibuat.
- Data referensi utama yang digunakan sebagai sumber dokumen dan template adalah arsip `storage/app/referensi-masjid/MASJID.zip`.
- Arsip sekarang tersedia, lolos pemeriksaan `unzip -t`, dan dapat dibaca langsung oleh generator template.

## 2) Struktur project Laravel yang sudah ada

### Framework & frontend
- Laravel 9.x dengan dependency management via Composer.
- PHP 8.x runtime.
- Blade template engine.
- Tailwind CSS + Vite.
- Authentication bawaan Laravel Breeze.
- UI admin sudah dibangun dengan layout, sidebar, card, tabel, dan form individual yang konsisten.

### Modul yang sudah terdigitalisasi di aplikasi
- Dashboard operasional.
- Manajemen galeri foto.
- Jadwal khutbah dan kultum.
- Jadwal Ramadhan.
- Kotak Amal Tarawih.
- Qurban: panitia, kontribusi, dan transaksi kas.
- Zakat: periode, penerimaan, distribusi, dan laporan.
- Surat: template, nomor, dan cetak surat administrasi.

## 3) Audit sumber referensi dokumen

Berdasarkan pemeriksaan arsip yang terpasang, sumber referensi masjid berisi 94 entri ZIP, termasuk direktori, dengan 81 file aktual:

- 32 file DOCX.
- 4 workbook XLSX.
- 19 PDF.
- 17 JPEG.
- 4 PNG.
- 1 PPTX.
- 1 CSV.
- 1 TXT.

### Template dan dokumen utama yang ditemukan
- Surat Edaran Zakat Fitrah.docx
- Hasil Akhir Zakat Fitrah.docx
- Tanda Penyerahan Zakat Fitrah.docx
- Surat Pengusulan UPZ Masjid.docx
- Draft Surat Pengurus Masjid.docx
- Nomor Surat Masjid.xlsx
- Jadwal Khotib dan Imam 2026.docx
- Jadwal Kultum 2025.docx
- Jadwal Takjil Ramadhan.docx
- Hasil Kotak Amal Shalat Tarawih.docx
- Dokumen Qurban dan file panitia Idul Adha.

### Aset visual penting
- KOP FIX.png
- KOP.png
- cap masjid.jpeg
- cap msjid fix.png
- cap_msjid_fix-removebg-preview.png

### Kesimpulan template
- Template harus dijaga semirip mungkin dengan dokumen asli.
- ukuran dokumen, margin, font, kop, stempel, dan tanda tangan harus dipertahankan.
- Template aktif tidak boleh dibangun ulang secara desain baru; harus memakai file sumber asli atau stuktur yang sangat dekat dengannya.

## 4) Rancangan data dan modul yang diperlukan

### Master data masjid
- nama masjid
- alamat
- RW/RT
- kelurahan
- kecamatan
- kota
- provinsi
- telepon
- email
- logo
- kop surat
- stempel
- penandatangan resmi

### Pengurus masjid
- periode kepengurusan
- jabatan
- nama
- gelar
- nomor HP
- alamat
- status aktif
- urutan tampil
- tanda tangan
- foto

### Nomor surat
- sistem nomor otomatis per kategori
- pola nomor dapat dikonfigurasi
- pola berbeda per surat dan per periode
- validasi duplikasi nomor
- histori nomor surat

### Sekretariat
- surat masuk
- surat keluar
- template surat
- arsip surat
- nomor surat
- proposal
- dokumen pendukung
- surat pengantar
- surat pengusulan
- surat rekomendasi
- SK
- dokumen pengurus

### Qurban
- data hewan
- panitia qurban
- penerimaan
- distribusi
- laporan kas
- surat qurban

### Ramadhan
- jadwal takjil
- jadwal kultum
- data donatur
- kotak amal
- agenda Ramadhan

### Zakat
- muzakki
- penerimaan
- distribusi
- laporan
- rekap per jenis
- validasi periode zakat

## 5) Rekap implementasi yang sudah ada di repo

Tabel dan modul yang sudah ada (atau mulai dibangun) di aplikasi saat ini:

- `mosque_letters`
- `letter_templates`
- `letter_sequences`
- `zakat_periods`
- `zakat_receipts`
- `zakat_distributions`
- `zakat_reports`
- `qurban_committee_members`
- `qurban_contributions`
- `qurban_transactions`

### Catatan teknis
- Sistem sudah memiliki beberapa struktur yang sesuai kebutuhan umum administrasi masjid.
- Beberapa komponen seperti template, laporan, dan generator surat masih perlu dilanjutkan dengan validasi dokumen asli dan sumber referensi.
- File `Distribuzi Zakaat (1).xlsx` belum tersedia di dalam arsip, sehingga konfigurasi final kolom distribusi dan rumus laporan zakat belum bisa dipastikan sepenuhnya.

## 6) Konflik dan catatan penting

- `KOP FIX.png` dan `KOP.png` memiliki alamat yang berbeda; ini menunjukkan bahwa identitas masjid perlu memiliki master konfigurasi yang bisa dikoreksi tanpa mengubah file masjid asli.
- Nomor surat tidak bisa memakai satu pola universal; pola ditemukan beragam per kategori dan per kegiatan.
- Format surat A4, Legal, dan lanskap dipakai pada template berbeda.
- Template lama dan template aktif harus dipisahkan agar arsip lama tidak rusak saat template baru dibuat.
- Gunakan template dokumen asli sebagai pusat redaksi; jangan menata ulang desain baru karena itu bertentangan dengan ketentuan yang sudah ditetapkan.

## 7) Rencana implementasi yang aman

1. Audit dokumen & data master dari file sumber.
2. Tetap mempertahankan UI/UX existing.
3. Menambahkan fitur yang belum ada tanpa mengganggu modul aktif.
4. Menggunakan database relasional yang terpisah per domain.
5. Membuat template manager dengan field mapping dan versi template.
6. Mengaktifkan generator surat melalui data input user, bukan menulis ulang template baru.
7. Menjaga file Word sumber sebagai template arsip dan file aktif yang tidak diubah secara langsung.
8. Menggunakan template versi berdasarkan tanggal dan status.

## 8) Keputusan best practice

- Jika file referensi ZIP benar-benar tersedia di lingkungan production, file itu harus dipindahkan ke storage yang aman dan dipelihara sebagai arsip resmi, bukan dibuat ulang.
- Template resmi sekarang dapat diambil langsung dari arsip oleh `ArchiveTemplateResolver`; aktivasi tetap harus melalui verifikasi admin per template.
- Penggunaan database dan routing harus aman di environment testing dan fresh install.

## 9) Kesimpulan

Proyek ini sudah memiliki fondasi administrasi masjid yang kuat: dashboard, operasional, qurban, zakat, surat, dan template dasar. Kebutuhan utama berikutnya adalah validasi dan penguatan terhadap:

- file template asli,
- master identitas masjid,
- nomor surat berbasis aturan historis,
- versi template,
- sistem generator surat yang menjaga layout asli,
- dan proteksi page publik agar tetap dapat diakses ketika database belum di-migrasi.

Dengan pendekatan ini, project tetap aman, konsisten dengan UI/UX yang sudah ada, dan dapat dikembangkan sesuai kebutuhan administrasi Masjid Nurul Iman tanpa merusak desain hukum dokumen resmi yang ada.
