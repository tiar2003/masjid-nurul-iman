# Analisis Referensi Administrasi Masjid Nurul Iman

## Status sumber

- Arsip yang tersedia: `storage/app/referensi-masjid/MASJID.zip` (41.941.928 byte).
- Arsip berisi 176 entri; path absolut dan path traversal tidak ditemukan.
- 77 DOCX dapat dibuka dan dibaca; tiga file `~$` adalah lock file Office dan bukan template.
- Ditemukan 17 workbook XLSX, 21 PDF, 17 JPEG, empat PNG, satu PPTX, satu CSV, satu TXT, serta file arsip/ekstensi lain.
- Ada beberapa salinan dengan konten identik, terutama template surat zakat lama dan berkas Muzakki/distribusi tahun 2020.
- File yang diminta sebagai sumber utama zakat, `Distribuzi Zakaat (1).xlsx`, **belum tersedia** di workspace maupun di dalam ZIP. XLSX zakat yang ada adalah template/arsip 2016-2020 dan tidak dapat dianggap sebagai pengganti workbook tersebut.

## Template yang ditelaah

| Dokumen sumber | Temuan struktur | Implikasi untuk website |
| --- | --- | --- |
| `SURAT EDARAN ZAKAT FITRAH.docx` (Ramadan 2025) | Redaksi mencakup fitrah, mal, fidyah, infaq/shodaqoh, jam pelayanan, batas penerimaan, tembusan RW dan arsip. Contoh isi menyebut 3 kg/jiwa, fidyah 2,5 kg atau Rp40.000/hari. | Field nominal, tanggal, periode Hijriah/Masehi, jam, dan batas penerimaan harus menjadi nilai input; redaksi tetap berasal dari template. |
| `HASIL AKHIR ZAKAT FITRAH.docx` (1446 H/2025) | Memisahkan penerimaan, penerima manfaat, pendistribusian, dan total per kategori; arsip tidak memakai satu tabel Excel sederhana. | Laporan harus merangkum transaksi penerimaan dan distribusi dari database, bukan menyimpan angka hasil ketik manual. |
| `TANDA PENYERAHAN ZAKAT FITRAH.docx` | Dua bagian pada satu halaman: Tanda Penyerahan dan Tanda Terima. Memuat beras/jiwa, uang/jiwa, mal, fidyah, infaq/shodaqoh, tanggal, serta pihak yang menyerahkan/menerima. | Buat satu template dua-bagian dengan data yang sama, bukan dua desain terpisah. |
| `SURAT PENGUSULAN UPZ MASJID.docx` | Surat administrasi formal dengan susunan khusus. | Pertahankan dokumen sumber sebagai basis template; jangan mengubahnya menjadi surat generik. |
| `Draft Surat Pengurus Masjid.docx` | Ukuran halaman terdeteksi A4; perlu memilih versi final yang berpasangan dengan PDF sebelum aktivasi. | Template draft tidak otomatis dijadikan template aktif. |
| `NOMOR SURAT MASJID.xlsx` | Register memuat lebih dari satu pola, termasuk `10/IX/Pan-Ramadhan/MNI/25`, `11/IX/Pan-Ramadhan/MNI/26`, `16/IX/Idul Adha/MNI/26`, `14/PAN PEL/PHBI/MNI/28/25`, serta entri `002/MNI/I/III/2026`. | Nomor surat tidak boleh memakai satu pola universal. Kode/kategori memiliki seri dan pola yang dapat dikonfigurasi. Entri `002/MNI/I/III/2026` perlu dikonfirmasi sebelum pola kategori MNI 2026 dianggap final. |
| `JADWAL KHOTIB DAN IMAM MASJID NURUL IMAN 2026 OKT-DES .docx` | Template jadwal resmi yang telah dipetakan ke 13 tanggal; tanggal Desember di dokumen sumber perlu diselaraskan dengan kolom bulan. | Tetap terpisah dari jadwal acak dan hanya dipublikasikan sebagai jadwal resmi. |
| `Jadwal Kultum 2025.docx` | Tabel berkolom No, Hari, Tanggal, Penceramah. | Model jadwal kegiatan menampung tanggal dan penanggung jawab/penceramah. |
| `JADWAL TAKJIL MASJID NURUL IMAN RAMADHAN.docx` | Tabel berkolom No, Hari/Tanggal, Nama, Jenis Makanan, Jumlah, Keterangan. | Form takjil menggunakan field tersebut dan dapat dicetak dari data tersimpan. |
| `HASIL KOTAK AMAL SHALAT TARAWIH.docx` | Tabel berkolom No, Hari, Tanggal, Hasil Kotak Amal, Jumlah. | Rekap harus dihitung dari transaksi, bukan total statis. |
| `DONATUR SANTUNAN ANAK YATIM ...docx` | Tabel berulang berkolom No, Nama, Sumbangan, Keterangan. | Data donatur dan penerima santunan dibatasi pada panel admin. |
| Dokumen Qurban dan spreadsheet `PANITIA IDUL ADHA 26` | Berisi susunan panitia, peserta hewan, penerimaan, dan format tanda terima. Sebagian dokumen Qurban lama berbeda periode/format. | Pisahkan master panitia, peserta/kelompok hewan, dan kas Qurban per tahun. Aktifkan template berdasarkan versi/tahun yang dipilih. |

## Ukuran halaman dan aset cetak

Ukuran Word berikut dibaca langsung dari `word/document.xml` dan dinyatakan sebagai ukuran cetak, bukan perkiraan CSS:

- `HASIL AKHIR ZAKAT FITRAH.docx`: Legal portrait, 8,5 × 14 inci; margin atas sekitar 42,5 mm dan sisi/bawah 25,4 mm.
- `SURAT EDARAN ZAKAT FITRAH.docx`: Legal portrait, 8,5 × 14 inci; margin atas sekitar 10 mm, kiri/kanan sekitar 20 mm, bawah sekitar 15 mm.
- `TANDA PENYERAHAN ZAKAT FITRAH.docx`: lanskap khusus sekitar 330 × 216 mm; margin atas/kiri/kanan sekitar 10 mm dan bawah sekitar 7,5 mm.
- `Surat Pengusulan UPZ Masjid.docx`: Legal portrait, 8,5 × 14 inci; margin khas yang berbeda dari surat edaran.
- `Draft Surat Pengurus Masjid.docx`: A4 portrait, sekitar 210 × 297 mm.
- Dokumen Qurban 2023 yang ditelaah: Legal portrait, 8,5 × 14 inci.
- Font eksplisit yang ditemukan pada template surat utama adalah Times New Roman. Gaya yang diwariskan dari style Word perlu dipertahankan saat membangun renderer.
- `KOP FIX.png` dan `KOP.png` tersedia. Alamat pada kedua versi berbeda: KOP FIX mencantumkan Hanoman IX No. 19, sedangkan KOP.png dan beberapa isi surat mencantumkan No. 30. Pengaturan identitas masjid di website harus dapat mengoreksi perbedaan ini; jangan menimpa file sumber.
- `cap_msjid_fix-removebg-preview.png` tersedia sebagai cap transparan dan lebih sesuai untuk diposisikan di atas tanda tangan. `cap masjid.jpeg` juga tersedia sebagai scan latar penuh.
- Ada foto KTP/dokumen identitas di arsip; berkas tersebut tidak dibaca, dipublikasikan, atau dimasukkan ke data aplikasi.

## Struktur data yang telah dipetakan

### Muzakki/penerimaan zakat

Workbook dalam ZIP yang dapat dibaca memakai kolom historis seperti No, Tanggal, Nama, Alamat, Zakat Fitrah, Jiwa, Zakat Mal, Fidyah, Shodaqoh, Petugas, dan Keterangan. Worksheet juga membedakan `orang yg berzakat` dan `nama distributor`. Ini petunjuk struktur historis, bukan pengganti workbook `Distribuzi Zakaat (1).xlsx` yang belum tersedia.

### Distribusi zakat

Dokumen Word/Excel historis membedakan penerimaan dari pendistribusian dan menyimpan jumlah penerima, kategori/asnaf, jenis bantuan, tanggal, dan keterangan. Rekap sisa harus dihitung dari jumlah penerimaan dikurangi penyaluran pada periode yang sama. Formula/kolom akhir harus dicocokkan dengan workbook source of truth sebelum dinyatakan final.

### Konfigurasi yang tidak boleh di-hard-code

- Identitas, alamat, telepon, kop, cap, penandatangan, dan jabatan.
- Periode zakat/tahun Hijriah dan Masehi.
- Ketentuan nominal/berat fitrah/fidyah.
- Pola nomor surat per kategori/kegiatan.
- Versi/jenis template dan status aktifnya.

## Gap terhadap website saat ini

- Form persuratan yang ada sebelumnya bersifat generik dan tidak memilih template sumber.
- Belum tersedia katalog template berversi, master identitas/penandatangan, arsip dokumen, atau generator laporan zakat berbasis transaksi.
- Data distribusi zakat lama menggunakan satu tabel campuran dan belum menyediakan periode/rekonsiliasi persediaan per tahun.
- Modul Ramadan/Qurban/santunan/persuratan kini sudah memiliki tabel dan formulir admin tersendiri; perlu diselesaikan dengan template cetak berbasis sumber.
- Workbook `Distribuzi Zakaat (1).xlsx` belum tersedia, sehingga desain final field/rumus distribusi zakat dan laporan belum bisa diverifikasi.

## Keputusan implementasi

1. File ZIP dipertahankan sebagai arsip privat dan tidak diubah.
2. Input baru dikelola dari dashboard website; tidak mengimpor otomatis daftar Muzakki, donatur, anak, atau KTP dari arsip historis.
3. Template aktif memakai file sumber terpilih, versi/tahun, ukuran kertas, margin, aset kop/cap, dan redaksi yang tercatat pada dokumen sumber.
4. Cetak surat/laporan menggunakan template berbeda sesuai format asli; halaman A4, Legal, dan tanda terima lanskap tidak diseragamkan.
5. Implementasi zakat mengikuti field yang dinyatakan pengguna dan struktur historis ZIP, tetapi tetap menunggu `Distribuzi Zakaat (1).xlsx` untuk mengunci kolom dan rumus distribusi.