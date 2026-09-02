<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            Sistem Informasi Masjid Nurul Iman
        </h2>
    </x-slot>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div x-data="{
                activeMenu: localStorage.getItem('masjidMenu') || 'jadwal-input',
                hoverMenu: null,
                setMenu(menu) {
                    this.activeMenu = menu;
                    localStorage.setItem('masjidMenu', menu);
                    this.hoverMenu = null;
                }
            }">

                <nav class="bg-white shadow-md border-b-4 border-blue-600 flex flex-wrap rounded-t-lg relative z-50">
                    <div class="relative" @mouseenter="hoverMenu = 1" @mouseleave="hoverMenu = null">
                        <button
                            class="px-6 py-5 text-gray-700 font-bold hover:text-blue-700 hover:bg-blue-50 focus:outline-none flex items-center transition-colors"
                            :class="{'text-blue-700 bg-blue-50': activeMenu.startsWith('jadwal')}">
                            Jadwal & Penceramah
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="hoverMenu === 1" x-transition.opacity.duration.200ms
                            class="absolute left-0 mt-0 w-56 bg-white border border-gray-200 shadow-xl rounded-b-md overflow-hidden z-50"
                            x-cloak>
                            <a href="#" @click.prevent="setMenu('jadwal-input')"
                                class="block px-5 py-3 text-sm text-gray-700 hover:bg-blue-600 hover:text-white border-b transition-colors">Input
                                Data Penceramah</a>
                            <a href="#" @click.prevent="setMenu('jadwal-kelola')"
                                class="block px-5 py-3 text-sm text-gray-700 hover:bg-blue-600 hover:text-white transition-colors">Acak
                                & Cetak Jadwal</a>
                        </div>
                    </div>

                    <div class="relative" @mouseenter="hoverMenu = 2" @mouseleave="hoverMenu = null">
                        <button
                            class="px-6 py-5 text-gray-700 font-bold hover:text-blue-700 hover:bg-blue-50 focus:outline-none flex items-center transition-colors"
                            :class="{'text-blue-700 bg-blue-50': activeMenu.startsWith('keuangan')}">
                            Keuangan Masjid
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="hoverMenu === 2" x-transition.opacity.duration.200ms
                            class="absolute left-0 mt-0 w-56 bg-white border border-gray-200 shadow-xl rounded-b-md overflow-hidden z-50"
                            x-cloak>
                            <a href="{{ asset('Nurul_Iman/index.html') }}" target="_blank"
                                class="block px-5 py-3 text-sm text-gray-700 hover:bg-blue-600 hover:text-white border-b transition-colors">
                                Kotak Amal Tarawih ↗
                            </a>
                            <a href="#" @click.prevent="setMenu('keuangan-zakat')"
                                class="block px-5 py-3 text-sm text-gray-700 hover:bg-blue-600 hover:text-white transition-colors">Zakat
                                Fitrah & Mal</a>
                        </div>
                    </div>

                    <div class="relative" @mouseenter="hoverMenu = 3" @mouseleave="hoverMenu = null">
                        <button
                            class="px-6 py-5 text-gray-700 font-bold hover:text-blue-700 hover:bg-blue-50 focus:outline-none flex items-center transition-colors"
                            :class="{'text-blue-700 bg-blue-50': activeMenu.startsWith('galeri')}">
                            Galeri Foto
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="hoverMenu === 3" x-transition.opacity.duration.200ms
                            class="absolute left-0 mt-0 w-56 bg-white border border-gray-200 shadow-xl rounded-b-md overflow-hidden z-50"
                            x-cloak>
                            <a href="#" @click.prevent="setMenu('galeri-upload')"
                                class="block px-5 py-3 text-sm text-gray-700 hover:bg-blue-600 hover:text-white border-b transition-colors">Upload
                                Foto Baru</a>
                            <a href="#" @click.prevent="setMenu('galeri-lihat')"
                                class="block px-5 py-3 text-sm text-gray-700 hover:bg-blue-600 hover:text-white transition-colors">Lihat
                                Galeri</a>
                        </div>
                    </div>
                </nav>

                <div x-show="activeMenu === 'jadwal-input'" x-cloak x-transition.opacity.duration.300ms
                    class="space-y-6">
                    <h3 class="text-2xl font-bold text-gray-800 border-b-2 border-gray-100 pb-2 mb-6">Manajemen Data
                        Penceramah</h3>

                    <!-- 1. Form Tambah Penceramah Manual -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="text-emerald-600">➕</span> Tambah Penceramah Baru
                        </h4>
                        <form action="{{ route('speaker.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama
                                        Penceramah</label>
                                    <input type="text" name="name" placeholder="Contoh: Ust. Ahmad Ayub"
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Gelar</label>
                                    <input type="text" name="title" placeholder="Contoh: S.Pd.I, M.Pd"
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Kategori</label>
                                <select name="type"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none bg-white">
                                    <option value="Khutbah">Khutbah (Jumat)</option>
                                    <option value="Kultum">Kultum (Harian)</option>
                                </select>
                            </div>

                            <div class="mt-4">
                                <button type="submit"
                                    style="background-color: #059669 !important; color: #ffffff !important; padding: 10px 24px; font-size: 14px; font-weight: 600; border-radius: 8px; border: none; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                                    Simpan Penceramah
                                </button>
                            </div>

                        </form>
                    </div>

                    <!-- 2. Form Impor Penceramah via CSV / Excel -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="text-emerald-600">📂</span> Impor Data Penceramah via CSV
                        </h3>
                        <form action="{{ route('speakers.import') }}" method="POST" enctype="multipart/form-data"
                            class="flex flex-col md:flex-row items-center gap-4">
                            @csrf
                            <input type="file" name="file_csv" accept=".csv, .txt"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer"
                                required>
                            <button type="submit"
                                class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white font-semibold text-sm py-2.5 px-6 rounded-lg shadow-md transition-colors whitespace-nowrap">
                                Impor Data
                            </button>
                        </form>
                        <p class="text-xs text-gray-500 mt-2">
                            Format kolom CSV:
                            <code>Nama, Gelar, Tipe, Limit Once (1/0), Pasaran Jawa (kosongkan jika tidak ada)</code>
                        </p>
                    </div>

                    <!-- 3. Tabel Daftar Penceramah & Aturan Interaktif -->
                    <div>
                        <h4 class="font-bold text-lg text-gray-800 mb-4">Daftar Penceramah Terdaftar & Aturan Khusus
                        </h4>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm bg-white">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-800 text-white text-sm">
                                        <th class="p-3 text-center w-12">No</th>
                                        <th class="p-3">Nama & Gelar</th>
                                        <th class="p-3">Tipe</th>
                                        <th class="p-3 text-center">Batas 1x Tampil</th>
                                        <th class="p-3 text-center">Pasaran Jawa (Skip)</th>
                                        <th class="p-3 text-center w-24">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y divide-gray-100">
                                    @forelse($speakers as $index => $s)
                                        <tr class="hover:bg-gray-50">
                                            <td class="p-3 text-center text-gray-600">{{ $index + 1 }}</td>
                                            <td class="p-3 font-medium text-gray-800">{{ $s->name }}, {{ $s->title }}</td>
                                            <td class="p-3">
                                                <span
                                                    class="bg-blue-100 text-blue-800 text-xs px-2.5 py-1 rounded-md font-medium">{{ $s->type }}</span>
                                            </td>

                                            <!-- Kolom Checkbox Batas 1x Tampil -->
                                            <td class="p-3 text-center">
                                                <input type="checkbox" {{ $s->limit_once ? 'checked' : '' }}
                                                    onchange="updateAturanPenceramah({{ $s->id }}, 'limit_once', this.checked)"
                                                    class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 cursor-pointer">
                                            </td>

                                            <!-- Kolom Pilihan Pasaran Jawa -->
                                            <td class="p-3 text-center">
                                                <select
                                                    onchange="updateAturanPenceramah({{ $s->id }}, 'skip_pasaran_jawa', this.value)"
                                                    class="text-xs border-gray-300 rounded-md shadow-sm focus:border-emerald-300 focus:ring focus:ring-emerald-200">
                                                    <option value="">-- Tidak Ada --</option>
                                                    <option value="Legi" {{ $s->skip_pasaran_jawa == 'Legi' ? 'selected' : '' }}>Legi</option>
                                                    <option value="Pahing" {{ $s->skip_pasaran_jawa == 'Pahing' ? 'selected' : '' }}>Pahing</option>
                                                    <option value="Pon" {{ $s->skip_pasaran_jawa == 'Pon' ? 'selected' : '' }}>Pon</option>
                                                    <option value="Wage" {{ $s->skip_pasaran_jawa == 'Wage' ? 'selected' : '' }}>Wage</option>
                                                    <option value="Kliwon" {{ $s->skip_pasaran_jawa == 'Kliwon' ? 'selected' : '' }}>Kliwon</option>
                                                </select>
                                            </td>

                                            <!-- Tombol Hapus -->
                                            <td class="p-3 text-center">
                                                <form action="{{ route('speakers.destroy', $s->id) }}" method="POST"
                                                    onsubmit="return confirm('Hapus data penceramah ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        style="background-color: #ef4444 !important; color: #ffffff !important; padding: 6px 12px; font-size: 12px; font-weight: 600; border-radius: 6px; border: none; cursor: pointer;">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="p-6 text-center text-gray-500">Belum ada data penceramah.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div x-show="activeMenu === 'jadwal-kelola'" x-cloak x-transition.opacity.duration.300ms>
                    <h3 class="text-2xl font-bold text-gray-800 border-b-2 border-gray-100 pb-2 mb-6">Kelola & Cetak
                        Jadwal</h3>

                    <div class="flex gap-8 border-b pb-8 mb-8">
                        <div class="flex-1 bg-gray-50 p-6 rounded-lg border">
                            <h4 class="font-bold text-lg mb-2">Jadwal Khutbah Jumat</h4>
                            <p class="text-sm text-gray-500 mb-4">Acak jadwal untuk 12 minggu (3 Bulan) ke depan.</p>
                            <form action="{{ route('schedule.khutbah') }}" method="POST" class="mb-3">@csrf <button
                                    class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-semibold">Acak
                                    Jadwal Khutbah</button></form>
                            <a href="{{ route('schedule.print', 'Khutbah') }}" target="_blank"
                                class="block text-center text-blue-600 hover:underline font-semibold mt-2">🖨️ Cetak
                                (Print A4)</a>
                        </div>

                        <div class="flex-1 bg-gray-50 p-6 rounded-lg border">
                            <h4 class="font-bold text-lg mb-2">Jadwal Kultum</h4>
                            <p class="text-sm text-gray-500 mb-4">Acak jadwal kultum untuk 30 hari berturut-turut.</p>
                            <form action="{{ route('schedule.kultum') }}" method="POST" class="mb-3">@csrf <button
                                    class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md font-semibold">Acak
                                    Jadwal Kultum</button></form>
                            <a href="{{ route('schedule.print', 'Kultum') }}" target="_blank"
                                class="block text-center text-blue-600 hover:underline font-semibold mt-2">🖨️ Cetak
                                (Print A4)</a>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- KONTEN 2A: KOTAK AMAL TARAWIH              -->
                <!-- ========================================== -->
                <div x-show="activeMenu === 'keuangan-amal'" x-cloak x-transition.opacity.duration.300ms>
                    <iframe src="{{ asset('Nurul_Iman/index.html') }}" width="100%" height="850px"
                        style="border:none; border-radius: 8px; overflow: hidden; background-color: white;"
                        scrolling="auto"></iframe>
                </div>

                <!-- ========================================== -->
                <!-- KONTEN 2B: DISTRIBUSI ZAKAT FITRAH         -->
                <!-- ========================================== -->
                <div x-show="activeMenu === 'keuangan-zakat'" x-cloak x-transition.opacity.duration.300ms>
                    <h3 class="text-2xl font-bold text-gray-800 border-b-2 border-gray-100 pb-2 mb-4">Laporan Penerimaan
                        Zakat & Infaq</h3>

                    <!-- Disclaimer / Petunjuk Pengisian -->
                    <div
                        class="bg-yellow-50 text-yellow-800 text-sm p-3 rounded-md border border-yellow-200 mb-6 flex items-start">
                        <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span><strong>Catatan Penting:</strong> Hanya isi nominal yang tertera pada lembar tanda terima.
                            <strong>Jika data tidak ada, biarkan kolom tetap kosong.</strong></span>
                    </div>

                    <form action="{{ route('distribusi.store') }}" method="POST"
                        class="bg-gray-50 p-6 rounded-lg border mb-4 shadow-sm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-semibold mb-1">Tanggal</label>
                                <input type="date" name="tanggal" class="w-full rounded-md border-gray-300" required>
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-sm font-semibold mb-1">Nama Pemberi</label>
                                <input type="text" name="nama" class="w-full rounded-md border-gray-300" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-4 rounded border mb-4">
                            <div class="font-bold text-indigo-700 md:col-span-4 border-b pb-1">1. Zakat Fitrah</div>
                            <div><label class="text-xs text-gray-500">Jumlah Jiwa</label><input type="number"
                                    name="fitrah_jiwa" placeholder="Kosongkan jika tidak ada"
                                    class="w-full rounded border-gray-300 placeholder-gray-300"></div>
                            <div><label class="text-xs text-gray-500">Beras (Kg)</label><input type="number" step="0.01"
                                    name="fitrah_beras_kg" placeholder="Kosongkan jika tidak ada"
                                    class="w-full rounded border-gray-300 placeholder-gray-300"></div>
                            <div class="md:col-span-2"><label class="text-xs text-gray-500">Uang (Rp)</label><input
                                    type="number" name="fitrah_uang_rp" placeholder="Kosongkan jika tidak ada"
                                    class="w-full rounded border-gray-300 placeholder-gray-300"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-4 rounded border mb-4">
                            <div class="font-bold text-indigo-700 md:col-span-4 border-b pb-1">2. Fidyah</div>
                            <div><label class="text-xs text-gray-500">Jumlah Jiwa</label><input type="number"
                                    name="fidyah_jiwa" placeholder="Kosongkan jika tidak ada"
                                    class="w-full rounded border-gray-300 placeholder-gray-300"></div>
                            <div><label class="text-xs text-gray-500">Beras (Kg)</label><input type="number" step="0.01"
                                    name="fidyah_beras_kg" placeholder="Kosongkan jika tidak ada"
                                    class="w-full rounded border-gray-300 placeholder-gray-300"></div>
                            <div class="md:col-span-2"><label class="text-xs text-gray-500">Uang (Rp)</label><input
                                    type="number" name="fidyah_uang_rp" placeholder="Kosongkan jika tidak ada"
                                    class="w-full rounded border-gray-300 placeholder-gray-300"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded border mb-4">
                            <div>
                                <div class="font-bold text-indigo-700 border-b pb-1 mb-2">3. Zakat Mal</div>
                                <label class="text-xs text-gray-500">Nominal Uang (Rp)</label><input type="number"
                                    name="zakat_mal_rp" placeholder="Kosongkan jika tidak ada"
                                    class="w-full rounded border-gray-300 placeholder-gray-300">
                            </div>
                            <div>
                                <div class="font-bold text-indigo-700 border-b pb-1 mb-2">4. Infaq & Shodaqoh</div>
                                <label class="text-xs text-gray-500">Nominal Uang (Rp)</label><input type="number"
                                    name="infaq_rp" placeholder="Kosongkan jika tidak ada"
                                    class="w-full rounded border-gray-300 placeholder-gray-300">
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-md font-semibold mt-2">Simpan
                            ke Laporan</button>
                    </form>

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">Data Zakat Fitrah</h3>

                        <!-- Tombol Hapus Semua -->
                        <form action="{{ route('distribusi-zakat.destroyAll') }}" method="POST"
                            onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus SELURUH data zakat fitrah? Tindakan ini tidak dapat dibatalkan!');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold py-2 px-4 rounded-lg shadow transition-colors cursor-pointer flex items-center gap-2">
                                <span>🗑️</span> Hapus Semua Data
                            </button>
                        </form>
                    </div>

                    <div class="overflow-x-auto rounded-lg border">
                        <table class="w-full text-left text-sm whitespace-nowrap bg-white">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="p-3 text-center">No</th>
                                    <th class="p-3">Nama</th>
                                    <th class="p-3 text-center">Fitrah (Jiwa)</th>
                                    <th class="p-3 text-right">Beras (Kg)</th>
                                    <th class="p-3 text-right">Zakat Uang</th>
                                    <th class="p-3 text-right">Zakat Mal</th>
                                    <th class="p-3 text-right">Infaq</th>
                                    <th class="p-3 text-right">Fidyah</th>
                                    <th class="p-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($distribusi_zakats as $index => $z)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-3 text-center">{{ $index + 1 }}</td>
                                        <td class="p-3 font-medium">{{ $z->nama }}</td>
                                        <td class="p-3 text-center">{{ $z->fitrah_jiwa > 0 ? $z->fitrah_jiwa : '-' }}</td>
                                        <td class="p-3 text-right text-green-700 font-semibold">
                                            {{ $z->fitrah_beras_kg > 0 ? (float) $z->fitrah_beras_kg . ' Kg' : '-' }}
                                        </td>
                                        <td class="p-3 text-right text-blue-700">
                                            {{ $z->fitrah_uang_rp > 0 ? 'Rp ' . number_format($z->fitrah_uang_rp, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="p-3 text-right text-purple-700">
                                            {{ $z->zakat_mal_rp > 0 ? 'Rp ' . number_format($z->zakat_mal_rp, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="p-3 text-right text-yellow-600">
                                            {{ $z->infaq_rp > 0 ? 'Rp ' . number_format($z->infaq_rp, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="p-3 text-right text-red-600">
                                            {{ $z->fidyah_uang_rp > 0 ? 'Rp ' . number_format($z->fidyah_uang_rp, 0, ',', '.') : ($z->fidyah_beras_kg > 0 ? (float) $z->fidyah_beras_kg . ' Kg' : '-') }}
                                        </td>
                                        <td class="p-3 text-center">
                                            <form id="delete-form-{{ $z->id }}"
                                                action="{{ route('distribusi.destroy', $z->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="konfirmasiHapus({{ $z->id }})"
                                                    style="background-color: #ef4444 !important; color: #ffffff !important; padding: 6px 12px; font-size: 12px; font-weight: 600; border-radius: 6px; border: none; cursor: pointer; display: inline-block;">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="p-6 text-center text-gray-500">Belum ada data laporan zakat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div x-show="activeMenu === 'galeri-upload'" x-cloak x-transition.opacity.duration.300ms>
                    <h3 class="text-2xl font-bold text-gray-800 border-b-2 border-gray-100 pb-2 mb-6">Upload Foto Masjid
                    </h3>
                    <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data"
                        class="max-w-2xl bg-gray-50 p-6 rounded-lg border">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih File Foto</label>
                            <input type="file" name="image"
                                class="block w-full border border-gray-300 rounded p-2 bg-white" accept="image/*"
                                required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori Acara</label>
                            <select name="category" class="block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option>TPQ Masjid Nurul Iman</option>
                                <option>Maulid Nabi</option>
                                <option>Buka Bersama</option>
                                <option>Kajian Rutin</option>
                            </select>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Caption / Keterangan
                                Tambahan</label>
                            <input type="text" name="caption" class="block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-semibold transition-colors">Upload
                            ke Galeri</button>
                    </form>
                </div>

                <div x-show="activeMenu === 'galeri-lihat'" x-cloak x-transition.opacity.duration.300ms>
                    <h3 class="text-2xl font-bold text-gray-800 border-b-2 border-gray-100 pb-2 mb-6">Galeri Foto Masjid
                        Nurul Iman</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @forelse($galleries as $g)
                            <div
                                class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow hover:shadow-lg transition-shadow group flex flex-col justify-between">
                                <div>
                                    <!-- Gambar -->
                                    <div class="relative overflow-hidden h-48 bg-gray-100">
                                        <img src="{{ asset('storage/' . $g->image_path) }}"
                                            class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300">
                                    </div>

                                    <!-- Keterangan -->
                                    <div class="p-4">
                                        <span
                                            class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded mb-2">{{ $g->category }}</span>
                                        <p class="text-sm text-gray-600 truncate">{{ $g->caption ?? 'Tanpa keterangan' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Tombol Hapus (Ditempatkan di dalam perulangan kartu foto) -->
                                <div class="p-4 pt-0">
                                    <form action="{{ route('galeri.destroy', $g->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus foto ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full bg-red-600 hover:bg-red-700 text-white text-xs font-bold py-2 px-3 rounded transition-colors cursor-pointer text-center">
                                            Hapus Foto
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div
                                class="col-span-full text-center text-gray-500 py-16 bg-gray-50 border border-dashed border-gray-300 rounded-lg">
                                <span class="text-5xl block mb-4">🖼️</span>
                                Belum ada foto yang diunggah ke Galeri.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if(session('deleted'))
            Toast.fire({
                icon: 'error',
                title: "{{ session('deleted') }}"
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'warning',
                title: "{{ session('error') }}"
            });
        @endif

        function konfirmasiHapus(id) {
            Swal.fire({
                title: 'Hapus data ini?',
                text: "Data penerimaan zakat ini akan dihapus permanen dari laporan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5', // Warna ungu senada dengan tombol dasbor
                cancelButtonColor: '#ef4444', // Warna merah untuk batal
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                shape: 'rounded-lg'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika tombol Ya ditekan, kirim form ke Laravel
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }

    </script>
</x-app-layout>