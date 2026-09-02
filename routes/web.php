<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GalleryController;

// Import model yang dibutuhkan
use App\Models\Gallery;
use App\Models\Schedule;
use App\Models\Terawih;
use App\Models\User;

// -----------------------------------------------------
// ROUTE HALAMAN UTAMA (LANDING PAGE)
// -----------------------------------------------------
Route::get('/', function () {
    // 1. Ambil 4 foto galeri terbaru
    $galleries = Gallery::latest()->take(4)->get();

    // 2. Ambil Jadwal Khutbah
    $jadwalKhutbah = Schedule::with('speaker')
        ->where('type', 'Khutbah')
        ->where('date', '>=', now()->toDateString())
        ->orderBy('date', 'asc')
        ->take(3)->get();

    // 3. Ambil Jadwal Kultum
    $jadwalKultum = Schedule::with('speaker')
        ->where('type', 'Kultum')
        ->where('date', '>=', now()->toDateString())
        ->orderBy('date', 'asc')
        ->take(3)->get();

    // 4. Ambil Data Kotak Amal Terawih
    $tarawihs = Terawih::orderBy('tanggal', 'asc')->get();

    // Asumsi field 'nominal' menyimpan jumlah uang
    $totalTarawih = $tarawihs->sum('amount');

    // Kirim semua data (variabel) ke view welcome
    return view('welcome', compact('galleries', 'jadwalKhutbah', 'jadwalKultum', 'tarawihs', 'totalTarawih'));
});


// -----------------------------------------------------
// ROUTE ADMIN (HARUS LOGIN)
// -----------------------------------------------------
Route::middleware(['auth'])->group(function () {

    // Dashboard Utama
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Galeri Foto
    Route::post('/gallery', [AdminController::class, 'storeGallery'])->name('gallery.store');
    Route::delete('/admin/galeri/{id}', [GalleryController::class, 'destroy'])->name('galeri.destroy');

    // Penceramah & Jadwal (Lengkap dengan Store, Update, Destroy & Generator)
    Route::post('/speakers', [AdminController::class, 'storeSpeaker'])->name('speaker.store');
    Route::put('/speakers/{speaker}', [AdminController::class, 'updateSpeaker'])->name('speaker.update');
    Route::post('/speakers/import', [AdminController::class, 'importSpeaker'])->name('speakers.import');
    
    // Rute untuk menghapus penceramah
    Route::delete('/speakers/{id}', [AdminController::class, 'destroySpeaker'])->name('speakers.destroy');

    Route::post('/schedules/generate/khutbah', [AdminController::class, 'generateKhutbah'])->name('schedule.khutbah');
    Route::post('/schedules/generate/kultum', [AdminController::class, 'generateKultum'])->name('schedule.kultum');
    Route::get('/schedules/print/{type}', [AdminController::class, 'printSchedule'])->name('schedule.print');
    Route::get('/schedules/print/Kultum', [AdminController::class, 'printKultum'])->name('schedules.print.kultum');

    // Kotak Amal Tarawih
    Route::post('/donations', [AdminController::class, 'storeDonation'])->name('donation.store');

    // Zakat
    Route::post('/zakat', [AdminController::class, 'storeZakat'])->name('zakat.store');
    Route::delete('/admin/distribusi-zakat/destroy-all', [AdminController::class, 'destroyAllDistribusi'])->name('distribusi-zakat.destroyAll');

    // Distribusi Zakat Fitrah (Baru)
    Route::post('/distribusi-zakat', [AdminController::class, 'storeDistribusiZakat'])->name('distribusi.store');
    Route::delete('/distribusi-zakat/{id}', [AdminController::class, 'destroyDistribusiZakat'])->name('distribusi.destroy');

    // Profil Bawaan Laravel
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/kotak-amal-terawih', function () {
        return view('kotak_amal');
    })->name('kotak-amal.custom');
});

require __DIR__ . '/auth.php';