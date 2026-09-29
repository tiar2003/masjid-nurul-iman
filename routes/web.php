<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\MosqueOperationsController;
use App\Http\Controllers\ZakatAdminController;

// Import model yang dibutuhkan
use App\Models\Gallery;
use App\Models\OfficialSchedule;
use App\Models\Schedule;
use App\Models\RamadanSchedule;
use App\Models\Terawih;
use App\Models\User;

// -----------------------------------------------------
// ROUTE HALAMAN UTAMA (LANDING PAGE)
// -----------------------------------------------------
Route::get('/', function () {
    // 1. Ambil 4 foto galeri terbaru
    $galleries = Gallery::latest()->take(4)->get();

    // Prioritaskan jadwal resmi; jadwal acak menjadi fallback.
    $jadwalKhutbah = OfficialSchedule::where('date', '>=', now()->toDateString())
        ->orderBy('date')
        ->take(3)
        ->get();
    $hasOfficialKhutbah = $jadwalKhutbah->isNotEmpty();

    if (!$hasOfficialKhutbah) {
        $jadwalKhutbah = Schedule::with('speaker')
            ->where('type', 'Khutbah')
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date', 'asc')
            ->take(3)->get();
    }

    // 3. Ambil Jadwal Kultum
    $jadwalKultum = Schedule::with('speaker')
        ->where('type', 'Kultum')
        ->where('date', '>=', now()->toDateString())
        ->orderBy('date', 'asc')
        ->take(3)->get();

    $ramadanSchedules = RamadanSchedule::where('is_public', true)
        ->where('date', '>=', now()->toDateString())
        ->orderBy('date')
        ->take(4)
        ->get();

    // 4. Ambil Data Kotak Amal Terawih
    $tarawihs = Terawih::orderBy('tanggal', 'asc')->get();

    // Asumsi field 'nominal' menyimpan jumlah uang
    $totalTarawih = $tarawihs->sum('amount');

    // Kirim semua data (variabel) ke view welcome
    return view('welcome', compact('galleries', 'jadwalKhutbah', 'hasOfficialKhutbah', 'jadwalKultum', 'ramadanSchedules', 'tarawihs', 'totalTarawih'));
});


// -----------------------------------------------------
// ROUTE ADMIN (HARUS LOGIN)
// -----------------------------------------------------
Route::middleware(['auth'])->group(function () {

    // Dashboard Utama
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Operasional Masjid: Ramadan, Qurban, santunan, dan persuratan
    Route::get('/admin/operasional', [MosqueOperationsController::class, 'index'])->name('operations.index');
    Route::post('/admin/operasional/{module}', [MosqueOperationsController::class, 'store'])->name('operations.store');
    Route::put('/admin/operasional/{module}/{id}', [MosqueOperationsController::class, 'update'])->name('operations.update');
    Route::delete('/admin/operasional/{module}/{id}', [MosqueOperationsController::class, 'destroy'])->name('operations.destroy');
    Route::get('/admin/surat/{letter}/cetak', [MosqueOperationsController::class, 'printLetter'])->name('operations.letters.print');
    Route::get('/admin/referensi-aset/{asset}', [MosqueOperationsController::class, 'referenceAsset'])->name('operations.reference-asset');
    Route::get('/admin/referensi-aset/{asset}', [MosqueOperationsController::class, 'referenceAsset'])->name('operations.reference-asset');

    // Zakat per periode, transaksi, distribusi, dan laporan
    Route::get('/admin/zakat', [ZakatAdminController::class, 'index'])->name('zakat-admin.index');
    Route::post('/admin/zakat/periode', [ZakatAdminController::class, 'storePeriod'])->name('zakat-admin.period.store');
    Route::post('/admin/zakat/periode/{period}/surat-edaran', [ZakatAdminController::class, 'storeCircular'])->name('zakat-admin.circular.store');
    Route::get('/admin/zakat/surat/{letter}/cetak', [MosqueOperationsController::class, 'printLetter'])->name('zakat-admin.circular.print');
    Route::post('/admin/zakat/periode/{period}/surat-edaran', [ZakatAdminController::class, 'storeCircular'])->name('zakat-admin.circular.store');
    Route::get('/admin/zakat/surat/{letter}/cetak', [MosqueOperationsController::class, 'printLetter'])->name('zakat-admin.circular.print');
    Route::post('/admin/zakat/penerimaan', [ZakatAdminController::class, 'storeReceipt'])->name('zakat-admin.receipts.store');
    Route::put('/admin/zakat/penerimaan/{id}', [ZakatAdminController::class, 'updateReceipt'])->name('zakat-admin.receipts.update');
    Route::delete('/admin/zakat/penerimaan/{id}', [ZakatAdminController::class, 'destroyReceipt'])->name('zakat-admin.receipts.destroy');
    Route::post('/admin/zakat/distribusi', [ZakatAdminController::class, 'storeDistribution'])->name('zakat-admin.distributions.store');
    Route::put('/admin/zakat/distribusi/{id}', [ZakatAdminController::class, 'updateDistribution'])->name('zakat-admin.distributions.update');
    Route::delete('/admin/zakat/distribusi/{id}', [ZakatAdminController::class, 'destroyDistribution'])->name('zakat-admin.distributions.destroy');
    Route::get('/admin/zakat/periode/{period}/laporan/cetak', [ZakatAdminController::class, 'printReport'])->name('zakat-admin.report.print');
    Route::get('/admin/zakat/arsip/{report}/cetak', [ZakatAdminController::class, 'printArchivedReport'])->name('zakat-admin.report.archive.print');
    Route::get('/admin/zakat/penerimaan/{receipt}/tanda', [MosqueOperationsController::class, 'printZakatHandover'])->name('zakat-admin.receipts.print');
    Route::get('/admin/zakat/penerimaan/{receipt}/tanda', [MosqueOperationsController::class, 'printZakatHandover'])->name('zakat-admin.receipts.print');
    Route::post('/admin/zakat/pengaturan-identitas', [ZakatAdminController::class, 'updateSettings'])->name('zakat-admin.settings.update');
    Route::post('/admin/zakat/pengurus', [ZakatAdminController::class, 'storeOfficial'])->name('zakat-admin.officials.store');
    Route::patch('/admin/surat/template/{template}', [ZakatAdminController::class, 'updateTemplate'])->name('zakat-admin.templates.update');

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
    Route::post('/schedules/import-official', [AdminController::class, 'importOfficialSchedule'])->name('schedule.import-official');
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