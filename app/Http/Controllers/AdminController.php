<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// PASTIKAN MODEL TERAWIH DITAMBAHKAN DI SINI
use App\Models\{Gallery, Speaker, Schedule, TarawihDonation, Zakat, Terawih};
use Carbon\Carbon;
use App\Models\DistribusiZakat;

class AdminController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'galleries' => Gallery::latest()->get(),
            'speakers' => Speaker::all(),
            'schedules' => Schedule::with('speaker')->orderBy('date')->get(),
            // Baris 'donations' => TarawihDonation... sudah kita HAPUS
            'zakats' => Zakat::orderBy('date')->get(),
            'distribusi_zakats' => DistribusiZakat::orderBy('tanggal', 'desc')->get()
        ]);
    }

    // 1. Fitur Galeri
    public function storeGallery(Request $request)
    {
        $request->validate(['image' => 'required|image', 'category' => 'required']);
        $path = $request->file('image')->store('galleries', 'public');
        Gallery::create(['category' => $request->category, 'image_path' => $path, 'caption' => $request->caption]);

        return redirect('/dashboard')->with('success', 'Foto berhasil diupload.');
    }

    // 2. Tambah Penceramah (Cek Duplikat Nama)
    public function storeSpeaker(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:speakers,name',
            'type' => 'required'
        ]);

        Speaker::create([
            'name' => $request->name,
            'title' => $request->title,
            'type' => $request->type,
            'limit_once' => $request->limit_once == 1 ? 1 : 0,
            'skip_third_week' => $request->skip_third_week == 1 ? 1 : 0,
        ]);

        return redirect('/dashboard')->with('success', 'Penceramah berhasil ditambahkan.');
    }

    // 3. Randomizer Khutbah (Dinamis Mencakup Seluruh Hari Jumat dalam 3 Bulan) + ATURAN KHUSUS
    public function generateKhutbah()
    {
        $speakers = Speaker::where('type', 'Khutbah')->get();
        if ($speakers->count() < 4)
            return redirect('/dashboard')->with('error', 'Minimal input 4 penceramah khutbah agar bisa diacak.');

        Schedule::where('type', 'Khutbah')->delete(); // Reset jadwal lama

        // Cari seluruh hari Jumat selama 3 bulan ke depan secara dinamis
        $fridays = [];
        $startDate = Carbon::now();

        // Cari hari Jumat pertama minggu ini/depan
        $currentFriday = $startDate->copy()->next(Carbon::FRIDAY);

        // Ambil hari Jumat selama 3 bulan (12 hingga 13 minggu tergantung jumlah Jumat riil)
        // Kita loop dari bulan berjalan hingga 3 bulan kedepan
        for ($m = 0; $m < 3; $m++) {
            $monthCheck = Carbon::now()->addMonths($m);
            $daysInMonth = $monthCheck->daysInMonth;

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateObj = Carbon::create($monthCheck->year, $monthCheck->month, $d);
                if ($dateObj->isFriday() && $dateObj->greaterThanOrEqualTo(Carbon::today())) {
                    // Masukkan ke array jika belum ada
                    if (!in_array($dateObj->format('Y-m-d'), array_map(fn($f) => $f->format('Y-m-d'), $fridays))) {
                        $fridays[] = $dateObj->copy();
                    }
                }
            }
        }

        // Batasi tepat untuk 3 bulan ke depan (biasanya mencakup 12 atau 13 hari Jumat)
        // Urutkan berdasarkan tanggal
        usort($fridays, fn($a, $b) => $a->greaterThan($b) ? 1 : -1);

        $usage = [];
        foreach ($speakers as $s)
            $usage[$s->id] = 0;

        foreach ($fridays as $index => $friday) {
            $mingguKe = $index + 1;

            $available = $speakers->filter(function ($s) use ($usage, $friday) {
                $maxLimit = $s->limit_once ? 1 : 4; // Maksimal tampil disesuaikan
                if ($usage[$s->id] >= $maxLimit)
                    return false;

                // Cek aturan Pasaran Jawa jika ada
                if (!empty($s->skip_pasaran_jawa)) {
                    $pasaranHariIni = $this->getPasaranJawa($friday->format('Y-m-d'));
                    if ($pasaranHariIni == trim($s->skip_pasaran_jawa)) {
                        return false; // Skip jika pasaran Jawa cocok
                    }
                }

                return true;
            });

            if ($available->isEmpty()) {
                $available = $speakers->filter(fn($s) => $usage[$s->id] < 4);
                if ($available->isEmpty())
                    $available = $speakers; // Fail-safe jika semua sudah penuh
            }

            $chosen = $available->random();
            $usage[$chosen->id]++;

            Schedule::create([
                'type' => 'Khutbah',
                'date' => $friday->format('Y-m-d'),
                'speaker_id' => $chosen->id
            ]);
        }

        return redirect('/dashboard')->with('success', 'Jadwal Khutbah berhasil diacak mencakup seluruh Jumat bulan tersebut!');
    }

    // 4. Randomizer Kultum (30 Hari)
    public function generateKultum(Request $request)
    {
        $days = 30; // 30 hari untuk Ramadhan / harian
        $speakers = Speaker::where('type', 'Kultum')->get();
        if ($speakers->count() < 2)
            return redirect('/dashboard')->with('error', 'Input penceramah kultum masih kurang.');

        Schedule::where('type', 'Kultum')->delete();

        $usage = [];
        foreach ($speakers as $s)
            $usage[$s->id] = 0;

        $date = Carbon::now();

        for ($i = 0; $i < $days; $i++) {
            $available = $speakers->filter(fn($s) => $usage[$s->id] < 3);
            if ($available->isEmpty())
                $available = $speakers;

            $chosen = $available->random();
            $usage[$chosen->id]++;

            Schedule::create(['type' => 'Kultum', 'date' => $date->copy()->addDays($i), 'speaker_id' => $chosen->id]);
        }
        return redirect('/dashboard')->with('success', "Jadwal Kultum $days Hari berhasil diacak!");
    }

    // 5. Cetak Jadwal A4
    public function printSchedule($type)
{
    $schedules = Schedule::with('speaker')->where('type', $type)->orderBy('date')->get();

    // Jika yang dicetak adalah Kultum, arahkan ke templat khusus kultum
    if (strtolower($type) === 'kultum') {
        return view('admin.print_kultum', compact('schedules', 'type'));
    }

    // Jika Khutbah, gunakan templat standar
    return view('admin.print_schedule', compact('schedules', 'type'));
}

    // ==========================================
    // --- API KOTAK AMAL CUSTOM ---
    // ==========================================

    public function getTerawih()
    {
        return response()->json(Terawih::orderBy('tanggal', 'asc')->get());
    }

    public function postTerawih(Request $request)
    {
        $tanggal = $request->tanggal;
        $nominal = $request->nominal;

        if ($nominal == "") {
            Terawih::where('tanggal', $tanggal)->delete();
        } else {
            Terawih::updateOrCreate(
                ['tanggal' => $tanggal],
                ['nominal' => (int) $nominal]
            );
        }
        return response()->json(['status' => 'ok']);
    }

    public function resetTerawih()
    {
        Terawih::truncate();
        return response()->json(['status' => 'ok']);
    }

    // 6. Kalkulator Kotak Amal
    public function storeDonation(Request $request)
    {
        $total = $request->box_1 + $request->box_2 + $request->box_3;
        TarawihDonation::create(array_merge($request->all(), ['total_daily' => $total]));
        return redirect('/dashboard')->with('success', 'Data kotak amal berhasil disimpan.');
    }

    // 7. Kalkulator Zakat
    public function storeZakat(Request $request)
    {
        Zakat::create($request->all());
        return redirect('/dashboard')->with('success', 'Data Zakat berhasil disimpan.');
    }

    // 8. Memperbarui aturan atau data penceramah
    public function updateSpeaker(Request $request, Speaker $speaker)
    {
        $speaker->update([
            'limit_once' => $request->limit_once == 1 ? 1 : 0,
            'skip_third_week' => $request->skip_third_week == 1 ? 1 : 0,
        ]);

        return redirect('/dashboard')->with('success', "Aturan untuk {$speaker->name} berhasil diperbarui.");
    }

    // 9. Menghapus penceramah
    public function destroySpeaker($id)
    {
        // Cari data penceramah berdasarkan ID, lalu hapus
        $speaker = \App\Models\Speaker::find($id);

        if ($speaker) {
            $speaker->delete();
        }

        return back()->with('success', 'Penceramah berhasil dihapus.');
    }

    // 10. Distribusi Zakat Fitrah
    public function storeDistribusiZakat(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tanggal' => 'required|date',
        ]);

        // Logika Otomatis Ribuan untuk semua kolom uang
        $fitrah_uang = $request->fitrah_uang_rp ?? 0;
        $mal_uang = $request->zakat_mal_rp ?? 0;
        $fidyah_uang = $request->fidyah_uang_rp ?? 0;
        $infaq_uang = $request->infaq_rp ?? 0;

        if ($fitrah_uang > 0 && $fitrah_uang < 10000)
            $fitrah_uang *= 1000;
        if ($mal_uang > 0 && $mal_uang < 10000)
            $mal_uang *= 1000;
        if ($fidyah_uang > 0 && $fidyah_uang < 10000)
            $fidyah_uang *= 1000;
        if ($infaq_uang > 0 && $infaq_uang < 10000)
            $infaq_uang *= 1000;

        \App\Models\DistribusiZakat::create([
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'fitrah_jiwa' => $request->fitrah_jiwa ?? 0,
            'fitrah_beras_kg' => $request->fitrah_beras_kg ?? 0,
            'fitrah_uang_rp' => $fitrah_uang,
            'zakat_mal_rp' => $mal_uang,
            'fidyah_jiwa' => $request->fidyah_jiwa ?? 0,
            'fidyah_beras_kg' => $request->fidyah_beras_kg ?? 0,
            'fidyah_uang_rp' => $fidyah_uang,
            'infaq_rp' => $infaq_uang,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Data Penerimaan berhasil disimpan!');
    }

    public function destroyDistribusiZakat($id)
    {
        \App\Models\DistribusiZakat::findOrFail($id)->delete();
        return back()->with('success', 'Data Distribusi Zakat berhasil dihapus!');
    }

    public function destroyAllDistribusi()
    {
        \App\Models\DistribusiZakat::truncate(); // Menghapus seluruh data zakat fitrah sekaligus
        return back()->with('success', 'Semua data zakat fitrah berhasil dihapus!');
    }

    // Fungsi untuk mendeteksi Pasaran Jawa berdasarkan tanggal
    private function getPasaranJawa($tanggal)
    {
        // Titik acuan referensi (Contoh: 1 Januari 2027 M adalah Kliwon / sesuaikan patokan pasaran lokal)
        $baseloop = new DateTime('2027-01-01');
        $target = new DateTime($tanggal);
        $diff = $baseloop->diff($target)->days;

        // Urutan siklus pasaran jawa: Legi, Pahing, Pon, Wage, Kliwon
        $pasaran = ['Legi', 'Pahing', 'Pon', 'Wage', 'Kliwon'];

        // Hitung sisa bagi siklus 5 hari pasaran
        $index = ($diff + 4) % 5;
        return $pasaran[$index];
    }

    //Saat sistem Anda memproses pembuatan jadwal otomatis atau manual, lakukan pengecekan apakah tanggal tersebut cocok dengan larangan pasaran Jawa milik penceramah
    public function cekJadwalPenceramah($penceramah_id, $tanggal_jadwal)
    {
        $penceramah = Speaker::find($penceramah_id);
        $pasaranHariIni = $this->getPasaranJawa($tanggal_jadwal);

        // Ambil daftar pasaran yang di-skip (jika disimpan sebagai array/JSON)
        $skips = json_decode($penceramah->skip_pasaran_jawa, true) ?? [];

        // Jika hari ini bertepatan dengan pasaran yang dilarang, batalkan plotting
        if (in_array($pasaranHariIni, $skips)) {
            return false; // Penceramah tidak boleh dijadwalkan pada hari ini
        }

        return true; // Aman untuk dijadwalkan
    }

    public function importSpeaker(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file_csv');
        $path = $file->getRealPath();

        $handle = fopen($path, 'r');
        $isFirstRow = true;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            // Lewati baris pertama jika itu adalah header judul kolom
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            // Pastikan kolom nama tidak kosong
            if (!empty($row[0])) {
                \App\Models\Speaker::create([
                    'name' => trim($row[0]),
                    'title' => trim($row[1] ?? ''),
                    'type' => trim($row[2] ?? 'Khutbah (Jumat)'),
                    'limit_once' => isset($row[3]) ? (bool) $row[3] : false,
                    'skip_pasaran_jawa' => !empty($row[4]) ? trim($row[4]) : null,
                ]);
            }
        }

        fclose($handle);

        return back()->with('success', 'Data penceramah berhasil diimpor!');
    }
}
