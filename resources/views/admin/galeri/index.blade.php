@extends('layouts.admin') {{-- Sesuaikan dengan layout admin utama Anda jika ada --}}

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6 border-b-2 border-gray-100 pb-4">
        <h3 class="text-2xl font-bold text-gray-800">Kelola Galeri Foto Masjid Nurul Iman</h3>
        <!-- Tombol Tambah Foto (Jika ada modal atau halaman create) -->
        <!-- <a href="{{ route('galeri.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded-lg shadow">Tambah Foto</a> -->
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($galleries as $g)
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow hover:shadow-lg transition-shadow group flex flex-col justify-between">
                <div>
                    <!-- Gambar -->
                    <div class="relative overflow-hidden h-48 bg-gray-100">
                        <img src="{{ asset('storage/' . $g->image_path) }}"
                            class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300">
                    </div>
                    
                    <!-- Keterangan -->
                    <div class="p-4">
                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded mb-2">{{ $g->category }}</span>
                        <p class="text-sm text-gray-600 truncate">{{ $g->caption ?? 'Tanpa keterangan' }}</p>
                    </div>
                </div>

                <!-- Tombol Hapus -->
                <div class="p-4 pt-0">
                    <form action="{{ route('galeri.destroy', $g->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus foto ini secara permanen?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white text-xs font-bold py-2 px-3 rounded transition-colors cursor-pointer text-center">
                            Hapus Foto
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-500 py-16 bg-gray-50 border border-dashed border-gray-300 rounded-lg">
                <span class="text-5xl block mb-4">🖼️</span>
                Belum ada foto yang diunggah ke Galeri.
            </div>
        @endforelse
    </div>
</div>
@endsection