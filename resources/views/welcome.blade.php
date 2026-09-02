<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Masjid Nurul Iman</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white shadow-md fixed w-full z-50 top-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo & Judul -->
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="font-bold text-xl text-gray-800">Masjid Nurul Iman</span>
                </div>
                
                <!-- Menu Tengah -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#beranda" class="text-gray-600 hover:text-green-600 font-medium transition">Beranda</a>
                    <a href="#galeri" class="text-gray-600 hover:text-green-600 font-medium transition">Galeri</a>
                    <a href="#jadwal" class="text-gray-600 hover:text-green-600 font-medium transition">Jadwal Khutbah</a>
                    <a href="#keuangan" class="text-gray-600 hover:text-green-600 font-medium transition">Laporan Keuangan</a>
                </div>

                <!-- Tombol Login (Menggunakan Blade URL Helper) -->
                <div class="flex items-center">
                    <a href="{{ url('/login') }}" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md font-semibold text-sm shadow-sm transition duration-300">
                        Login Admin
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="pt-24 pb-12 md:pt-32 md:pb-24 bg-green-700 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Selamat Datang di Masjid Nurul Iman</h1>
            <p class="text-lg md:text-xl text-green-100 max-w-2xl mx-auto">Pusat ibadah, pendidikan, dan kegiatan sosial masyarakat. Mari bersama memakmurkan masjid.</p>
        </div>
    </section>

    <!-- Section Galeri -->
    <section id="galeri" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800">Galeri Kegiatan</h2>
                <div class="w-24 h-1 bg-green-600 mx-auto mt-4 rounded"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Card TPQ -->
                <div class="bg-gray-50 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">
                    <img src="https://images.unsplash.com/photo-1577884812294-88981442a8b9?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="TPQ" class="w-full h-48 object-cover">
                    <div class="p-5">
                        <h3 class="font-bold text-lg mb-2 text-green-700">TPQ Masjid Nurul Iman</h3>
                        <p class="text-sm text-gray-600">Kegiatan belajar membaca Al-Quran untuk anak-anak dan remaja.</p>
                    </div>
                </div>

                <!-- Card Maulid Nabi -->
                <div class="bg-gray-50 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">
                    <img src="https://images.unsplash.com/photo-1596716035017-d7d42cf38a53?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Maulid Nabi" class="w-full h-48 object-cover">
                    <div class="p-5">
                        <h3 class="font-bold text-lg mb-2 text-green-700">Peringatan Maulid Nabi</h3>
                        <p class="text-sm text-gray-600">Dokumentasi tabligh akbar dan sholawatan bersama jamaah.</p>
                    </div>
                </div>

                <!-- Card Buka Bersama -->
                <div class="bg-gray-50 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">
                    <img src="https://images.unsplash.com/photo-1542152864-1cb58e4cc80c?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Buka Bersama" class="w-full h-48 object-cover">
                    <div class="p-5">
                        <h3 class="font-bold text-lg mb-2 text-green-700">Buka Puasa Bersama</h3>
                        <p class="text-sm text-gray-600">Momen kebersamaan berbuka puasa di bulan suci Ramadhan.</p>
                    </div>
                </div>

                <!-- Card Kajian Rutin -->
                <div class="bg-gray-50 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">
                    <img src="https://images.unsplash.com/photo-1609599006353-e629aaab31ce?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Kajian Rutin" class="w-full h-48 object-cover">
                    <div class="p-5">
                        <h3 class="font-bold text-lg mb-2 text-green-700">Kajian Rutin Mingguan</h3>
                        <p class="text-sm text-gray-600">Kajian tafsir dan fiqih setiap ba'da Maghrib bersama ustadz.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

<!-- Section Jadwal Khutbah & Kultum -->
    <section id="jadwal" class="py-16 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800">Jadwal Penceramah</h2>
                <div class="w-24 h-1 bg-green-600 mx-auto mt-4 rounded"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Tabel Jadwal Khutbah Jumat -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-green-600 text-white py-4 px-6 flex justify-between items-center">
                        <h3 class="font-bold text-lg">Jadwal Khutbah Jumat</h3>
                        <span class="text-xs bg-green-500 px-2 py-1 rounded">3 Bulan Kedepan</span>
                    </div>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700">
                                <th class="py-3 px-6 font-semibold w-1/3 border-b">Tanggal</th>
                                <th class="py-3 px-6 font-semibold w-2/3 border-b">Nama Khatib / Imam</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="py-3 px-6">4 September 2026</td>
                                <td class="py-3 px-6 font-medium">Ust. H. Abdul Somad, Lc.</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="py-3 px-6">11 September 2026</td>
                                <td class="py-3 px-6 font-medium">Dr. K.H. Muhammad Zaid</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 px-6">18 September 2026</td>
                                <td class="py-3 px-6 font-medium">Ust. Budi Rahman, M.Ag</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Tabel Jadwal Kultum -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-yellow-500 text-white py-4 px-6 flex justify-between items-center">
                        <h3 class="font-bold text-lg">Jadwal Kultum</h3>
                        <span class="text-xs bg-yellow-400 px-2 py-1 rounded">30 Hari Kedepan</span>
                    </div>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700">
                                <th class="py-3 px-6 font-semibold w-1/3 border-b">Tanggal</th>
                                <th class="py-3 px-6 font-semibold w-2/3 border-b">Nama Penceramah</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="py-3 px-6">2 September 2026</td>
                                <td class="py-3 px-6 font-medium">Ust. Hanan Attaki</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="py-3 px-6">3 September 2026</td>
                                <td class="py-3 px-6 font-medium">Ust. Adi Hidayat, Lc.</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 px-6">4 September 2026</td>
                                <td class="py-3 px-6 font-medium">Ust. Khalid Basalamah</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </section>

<!-- Section Perolehan Kotak Amal Tarawih -->
    <section id="keuangan" class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4">
            <div class="text-center mb-10">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 uppercase">Hasil Kotak Amal Solat Terawih 1448 H / 2027 M</h2>
                <div class="w-24 h-1 bg-blue-600 mx-auto mt-4 rounded"></div>
            </div>

            <div class="bg-white rounded shadow-sm overflow-hidden max-w-5xl mx-auto">
                <table class="w-full text-center border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-[#3b82f6] text-white">
                            <th class="py-3 px-4 border border-[#2563eb] font-semibold w-16">No</th>
                            <th class="py-3 px-4 border border-[#2563eb] font-semibold">Hari</th>
                            <th class="py-3 px-4 border border-[#2563eb] font-semibold">Tanggal</th>
                            <th class="py-3 px-4 border border-[#2563eb] font-semibold">Hasil Kotak Amal (Rp)</th>
                            <th class="py-3 px-4 border border-[#2563eb] font-semibold">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 border border-gray-200">1</td>
                            <td class="py-3 px-4 border border-gray-200">RABU</td>
                            <td class="py-3 px-4 border border-gray-200">18-02-2026</td>
                            <td class="py-3 px-4 border border-gray-200">Rp 1.000.000</td>
                            <td class="py-3 px-4 border border-gray-200">Rp 1.000.000</td>
                        </tr>
                        <!-- Baris data tambahan dari PHP akan di-loop di sini -->
                    </tbody>
                </table>
            </div>

            <div class="mt-8 text-right">
                <p class="text-2xl font-bold text-gray-800">Total Keseluruhan: Rp 1.000.000</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="font-semibold">Masjid Nurul Iman</p>
            <p class="text-gray-400 text-sm mt-2">© 2026 Sistem Informasi Masjid. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

</body>
</html>