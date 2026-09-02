<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Masjid Nurul Iman</title>
    <!-- Tailwind CSS -->
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
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="font-bold text-xl text-gray-800">Masjid Nurul Iman</span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#beranda" class="text-gray-600 hover:text-green-600 font-medium transition">Beranda</a>
                    <a href="#galeri" class="text-gray-600 hover:text-green-600 font-medium transition">Galeri</a>
                    <a href="#jadwal" class="text-gray-600 hover:text-green-600 font-medium transition">Jadwal Khutbah</a>
                    <a href="#keuangan" class="text-gray-600 hover:text-green-600 font-medium transition">Laporan Keuangan</a>
                </div>
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
                @forelse($galleries as $galeri)
                <div class="bg-gray-50 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">
                    <img src="{{ asset('storage/' . $galeri->image_path) }}" alt="Galeri" class="w-full h-48 object-cover">
                    <div class="p-5">
                        <h3 class="font-bold text-lg mb-2 text-green-700">{{ $galeri->category }}</h3>
                        <p class="text-sm text-gray-600">{{ $galeri->caption }}</p>
                    </div>
                </div>
                @empty
                <div class="col-span-4 text-center text-gray-500 py-8">Belum ada foto galeri yang diunggah.</div>
                @endforelse
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
                    </div>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700">
                                <th class="py-3 px-6 font-semibold w-1/3 border-b">Tanggal</th>
                                <th class="py-3 px-6 font-semibold w-2/3 border-b">Nama Khatib / Imam</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            @forelse($jadwalKhutbah as $khutbah)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="py-3 px-6">{{ \Carbon\Carbon::parse($khutbah->date)->translatedFormat('d F Y') }}</td>
                                <td class="py-3 px-6 font-medium">{{ $khutbah->speaker->name ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center py-4 text-gray-500">Jadwal belum tersedia.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Tabel Jadwal Kultum -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-yellow-500 text-white py-4 px-6 flex justify-between items-center">
                        <h3 class="font-bold text-lg">Jadwal Kultum</h3>
                    </div>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700">
                                <th class="py-3 px-6 font-semibold w-1/3 border-b">Tanggal</th>
                                <th class="py-3 px-6 font-semibold w-2/3 border-b">Nama Penceramah</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            @forelse($jadwalKultum as $kultum)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="py-3 px-6">{{ \Carbon\Carbon::parse($kultum->date)->translatedFormat('d F Y') }}</td>
                                <td class="py-3 px-6 font-medium">{{ $kultum->speaker->name ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center py-4 text-gray-500">Jadwal belum tersedia.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

<!-- Section Perolehan Kotak Amal Tarawih -->
    <section id="keuangan" class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800">Laporan Kotak Amal Terawih</h2>
                <div class="w-24 h-1 bg-green-600 mx-auto mt-4 rounded"></div>
            </div>

            <!-- Kartu Total Sementara -->
            <div class="bg-gradient-to-r from-green-600 to-green-500 rounded-xl shadow-lg p-8 text-center text-white max-w-lg mx-auto mb-10 transition hover:shadow-xl">
                <h3 class="text-xl font-medium opacity-90">Total Perolehan Sementara</h3>
                <p class="text-4xl md:text-5xl font-bold mt-2" id="total-amal-display">Rp 0</p>
            </div>

            <!-- Tabel Detail -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden max-w-4xl mx-auto">
                <table class="w-full text-center border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th class="py-4 px-6 font-semibold border-b">No</th>
                            <th class="py-4 px-6 font-semibold border-b">Hari</th>
                            <th class="py-4 px-6 font-semibold border-b">Tanggal</th>
                            <th class="py-4 px-6 font-semibold border-b">Hasil Kotak Amal (Rp)</th>
                            <th class="py-4 px-6 font-semibold border-b">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700" id="tabel-amal-body">
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">Memuat data dari sistem lama...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

   <!-- Script Fetch Data Lama (Dengan Auto-Refresh & Deteksi Hari) -->
    <script>
        function muatDataKotakAmal() {
            fetch('/Nurul_Iman/api.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('tabel-amal-body');
                    const totalDisplay = document.getElementById('total-amal-display');
                    
                    let htmlBaru = '';
                    let totalAkumulasi = 0;
                    
                    let arrayData = Array.isArray(data) ? data : (data.data || []);

                    if (arrayData.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="py-6 text-center text-gray-500">Belum ada data kotak amal.</td></tr>';
                        return;
                    }

                    // Array nama hari dalam bahasa Indonesia
                    const daftarHari = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];

                    arrayData.forEach((item, index) => {
                        let nominal = parseInt(item.hasil || item.jumlah || item.nominal || item.Hasil || 0);
                        totalAkumulasi += nominal;

                        // Deteksi nama hari otomatis dari item.tanggal
                        let namaHari = '-';
                        if (item.tanggal) {
                            let dateObj = new Date(item.tanggal);
                            namaHari = daftarHari[dateObj.getDay()];
                        }
                        
                        // Gunakan item.hari dari API jika ada, jika tidak gunakan namaHari otomatis
                        let hariFix = item.hari ? item.hari : namaHari;

                        htmlBaru += `
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="py-4 px-6">${index + 1}</td>
                                <td class="py-4 px-6 uppercase">${hariFix}</td>
                                <td class="py-4 px-6">${item.tanggal || '-'}</td>
                                <td class="py-4 px-6 text-gray-800">Rp ${nominal.toLocaleString('id-ID')}</td>
                                <td class="py-4 px-6 font-medium text-green-700">Rp ${totalAkumulasi.toLocaleString('id-ID')}</td>
                            </tr>
                        `;
                    });

                    tbody.innerHTML = htmlBaru;
                    totalDisplay.innerText = 'Rp ' + totalAkumulasi.toLocaleString('id-ID');
                })
                .catch(error => {
                    console.error('Gagal memuat data:', error);
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            muatDataKotakAmal();
            setInterval(muatDataKotakAmal, 10000);
        });
    </script>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="font-semibold">Masjid Nurul Iman</p>
            <p class="text-gray-400 text-sm mt-2">© 2026 Sistem Informasi Masjid. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

</body>
</html>