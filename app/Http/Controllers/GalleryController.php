<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Menampilkan daftar galeri di halaman admin.
     */
    public function index()
    {
        $galleries = Gallery::latest()->get();
        return view('admin.galeri.index', compact('galleries'));
    }

    /**
     * Menghapus data galeri dan file fisiknya dari storage.
     */
    public function destroy($id)
    {
        $gallery = Gallery::findOrFail($id);

        // Hapus file fisik gambar dari folder storage jika ada
        if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
            Storage::disk('public')->delete($gallery->image_path);
        }

        // Hapus data dari database
        $gallery->delete();

        return redirect()->back()->with('success', 'Foto galeri berhasil dihapus.');
    }
}