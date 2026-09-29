<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-emerald-700">Administrasi tahunan</p>
                <h2 class="mt-1 text-2xl font-semibold leading-tight text-gray-900">Pengelolaan Zakat</h2>
                <p class="mt-1 text-sm text-gray-500">Penerimaan, penyaluran, dan rekap dihitung dari transaksi periode yang dipilih.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-emerald-800 hover:text-emerald-600"><span aria-hidden="true">&larr;</span> Dashboard</a>
        </div>
    </x-slot>

    <div class="dashboard-workspace min-h-screen px-0 py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="dashboard-toolbar">
                <div class="dashboard-toolbar__status"><span class="dashboard-toolbar__dot"></span><span>Zakat</span><span class="dashboard-toolbar__separator">/</span><span>{{ $year }}{{ $period?->hijri_year ? ' · ' . $period->hijri_year . ' H' : '' }}</span></div>
                <form action="{{ route('zakat-admin.index') }}" method="GET" class="operations-year-filter m-0">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <label for="zakat-year">Tahun Masehi</label>
                    <select id="zakat-year" name="year" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                        @foreach ($years as $availableYear)<option value="{{ $availableYear }}" @selected($year === $availableYear)>{{ $availableYear }}</option>@endforeach
                    </select>
                    <noscript><button class="rounded-md bg-emerald-700 px-3 py-2 text-xs font-bold text-white">Pilih</button></noscript>
                </form>
            </div>

            <nav class="operations-tabs" aria-label="Modul zakat">
                @foreach (['ringkasan' => 'Ringkasan', 'periode' => 'Periode', 'penerimaan' => 'Penerimaan', 'distribusi' => 'Distribusi', 'laporan' => 'Laporan', 'pengaturan' => 'Data Masjid'] as $key => $label)
                    <a href="{{ route('zakat-admin.index', ['year' => $year, 'tab' => $key]) }}" @if ($tab === $key) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </nav>

            @if (session('success'))<div class="dashboard-feedback dashboard-feedback--success" role="status">{{ session('success') }}</div>@endif
            @if (session('error'))<div class="dashboard-feedback" role="alert">{{ session('error') }}</div>@endif
            @if ($errors->any())<div class="dashboard-feedback" role="alert"><strong>Periksa isian berikut.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

            @if ($tab === 'periode')
                <section class="operations-panel">
                    <div class="operations-panel__heading"><div><p class="eyebrow eyebrow--green">Kebijakan tahun berjalan</p><h3>{{ $period ? 'Ubah periode zakat' : 'Buat periode zakat' }}</h3></div></div>
                    <form action="{{ route('zakat-admin.period.store') }}" method="POST" class="operations-form">
                        @csrf
                        <div class="operations-fields">
                            <label>Tahun Masehi<input type="number" name="year" min="2000" max="2100" value="{{ old('year', $period->year ?? $year) }}" required></label>
                            <label>Tahun Hijriah<input type="text" name="hijri_year" maxlength="12" placeholder="1448 H" value="{{ old('hijri_year', $period->hijri_year ?? '') }}"></label>
                            <label>Status periode<select name="status"><option value="Draft" @selected(old('status', $period->status ?? 'Draft') === 'Draft')>Draft</option><option value="Aktif" @selected(old('status', $period->status ?? '') === 'Aktif')>Aktif</option><option value="Ditutup" @selected(old('status', $period->status ?? '') === 'Ditutup')>Ditutup</option></select></label>
                            <label>Awal pelayanan<input type="date" name="starts_at" value="{{ old('starts_at', isset($period->starts_at) ? $period->starts_at->format('Y-m-d') : '') }}"></label>
                            <label>Akhir pelayanan<input type="date" name="ends_at" value="{{ old('ends_at', isset($period->ends_at) ? $period->ends_at->format('Y-m-d') : '') }}"></label>
                            <label>Fitrah per jiwa (kg)<input type="number" name="fitrah_kg_per_person" min="0.01" step="0.01" value="{{ old('fitrah_kg_per_person', $period->fitrah_kg_per_person ?? '') }}" required></label>
                            <label>Zakat mal (%)<input type="number" name="zakat_mal_rate_percent" min="0" max="100" step="0.01" value="{{ old('zakat_mal_rate_percent', $period->zakat_mal_rate_percent ?? '') }}"></label>
                            <label>Fidyah per hari (kg)<input type="number" name="fidyah_kg_per_day" min="0" step="0.01" value="{{ old('fidyah_kg_per_day', $period->fidyah_kg_per_day ?? '') }}"></label>
                            <label>Fidyah per hari (Rp)<input type="number" name="fidyah_money_per_day" min="0" step="1000" value="{{ old('fidyah_money_per_day', $period->fidyah_money_per_day ?? '') }}"></label>
                            <label>Jam pelayanan<input type="text" name="service_hours" value="{{ old('service_hours', $period->service_hours ?? '') }}" placeholder="Contoh: 20.00–22.00 WIB"></label>
                            <label class="operations-field--wide">Catatan kebijakan<textarea name="notes" rows="3">{{ old('notes', $period->notes ?? '') }}</textarea></label>
                        </div>
                        <div class="operations-form__actions"><button type="submit" class="button button--primary">Simpan periode {{ $year }}</button></div>
                    </form>
                </section>
                @php $circularTemplate = $letterTemplates->firstWhere('template_key', 'ZAKAT_EDARAN_2025'); @endphp
                <section class="operations-panel">
                    <div class="operations-panel__heading"><div><p class="eyebrow eyebrow--green">Template: Surat Edaran Zakat Fitrah</p><h3>Buat surat dari periode {{ $year }}</h3></div></div>
                    @if (!$period)
                        <p class="operations-empty">Simpan periode terlebih dahulu.</p>
                    @elseif (!$circularTemplate || !$circularTemplate->is_active || !$circularTemplate->is_verified || !$circularTemplate->numbering_pattern)
                        <p class="operations-privacy">Template ini belum diverifikasi atau pola nomor belum disimpan. Buka tab Data Masjid untuk meninjau format Legal, redaksi, penandatangan, dan pola nomor dari register sebelum mengaktifkannya.</p>
                        <a class="button button--secondary mt-3 inline-flex" href="{{ route('zakat-admin.index', ['year' => $year, 'tab' => 'pengaturan']) }}">Tinjau template</a>
                    @else
                        <form method="POST" action="{{ route('zakat-admin.circular.store', $period) }}" class="operations-form">
                            @csrf
                            <div class="operations-fields">
                                <label>Tanggal surat<input type="date" name="issue_date" value="{{ old('issue_date', now()->format('Y-m-d')) }}" required></label>
                                <label>Tujuan surat<input type="text" name="recipient" value="{{ old('recipient', 'Warga RW IX Muslimin/Muslimat Di Lingkungan RW IX') }}" required></label>
                                <label>Jam pelayanan<input type="text" name="service_hours" value="{{ old('service_hours', $period->service_hours ?? '') }}" required></label>
                                <label>Tempat pelayanan<input type="text" name="venue" value="{{ old('venue', 'Masjid Nurul Iman RW IX Kel. Krapyak') }}" required></label>
                                <label>Batas Hijriah<input type="text" name="closing_hijri_day" value="{{ old('closing_hijri_day', '') }}" placeholder="Contoh: malam 29 Ramadhan" required></label>
                                <label>Ketua / penandatangan<select name="chairperson_id" required><option value="">Pilih ketua</option>@foreach ($officials->where('is_signatory', true) as $official)<option value="{{ $official->id }}" @selected((int) old('chairperson_id') === $official->id)>{{ $official->name }} · {{ $official->position }}</option>@endforeach</select></label>
                                <label>Sekretaris<select name="secretary_id"><option value="">Tidak dicantumkan</option>@foreach ($officials->where('is_signatory', true) as $official)<option value="{{ $official->id }}" @selected((int) old('secretary_id') === $official->id)>{{ $official->name }} · {{ $official->position }}</option>@endforeach</select></label>
                                <label>Mengetahui<select name="knowing_official_id" required><option value="">Pilih penandatangan</option>@foreach ($officials->where('is_signatory', true) as $official)<option value="{{ $official->id }}" @selected((int) old('knowing_official_id') === $official->id)>{{ $official->name }} · {{ $official->position }}</option>@endforeach</select></label>
                                <label class="operations-field--wide">Tembusan<textarea name="cc" rows="2">{{ old('cc', "Ketua RW IX Kel Krapyak\nArsip") }}</textarea></label>
                                <label class="operations-check operations-field--wide"><input type="checkbox" name="show_stamp" value="1" @checked(old('show_stamp'))> Tampilkan cap asli pada surat</label>
                            </div>
                            <p class="operations-privacy">Tahun Hijriah, berat fitrah, fidyah, batas penerimaan, dan pola nomor diambil dari periode/template. Periksa draft sebelum dicetak.</p>
                            <div class="operations-form__actions"><button class="button button--primary">Buat draft surat</button></div>
                        </form>
                    @endif
                </section>
            @elseif ($tab === 'penerimaan')
                <section class="operations-panel">
                    <div class="operations-panel__heading"><div><p class="eyebrow eyebrow--green">{{ $year }}{{ $period?->hijri_year ? ' · ' . $period->hijri_year . ' H' : '' }}</p><h3>{{ $receiptEditing ? 'Ubah penerimaan' : 'Catat penerimaan zakat' }}</h3></div></div>
                    @if (!$period || $period->status === 'Ditutup')
                        <p class="operations-empty">Buat atau aktifkan periode zakat di tab Periode sebelum mencatat transaksi.</p>
                    @else
                        <form action="{{ $receiptEditing ? route('zakat-admin.receipts.update', $receiptEditing) : route('zakat-admin.receipts.store') }}" method="POST" class="operations-form">
                            @csrf @if ($receiptEditing) @method('PUT') @endif
                            <input type="hidden" name="year" value="{{ $year }}">
                            <div class="operations-fields">
                                <label>Tanggal<input type="date" name="date" value="{{ old('date', isset($receiptEditing->date) ? $receiptEditing->date->format('Y-m-d') : now()->format('Y-m-d')) }}" required></label>
                                <label>Nama Muzakki<input type="text" name="muzakki_name" value="{{ old('muzakki_name', $receiptEditing->muzakki_name ?? '') }}" required></label>
                                <label>Alamat<input type="text" name="address" value="{{ old('address', $receiptEditing->address ?? '') }}"></label>
                                <label>Jumlah jiwa<input type="number" name="souls" min="0" value="{{ old('souls', $receiptEditing->souls ?? 0) }}"></label>
                                <label>Fitrah beras (kg)<input type="number" name="fitrah_rice_kg" min="0" step="0.01" value="{{ old('fitrah_rice_kg', $receiptEditing->fitrah_rice_kg ?? 0) }}"></label>
                                <label>Fitrah uang (Rp)<input type="number" name="fitrah_money" min="0" step="1000" value="{{ old('fitrah_money', $receiptEditing->fitrah_money ?? 0) }}"></label>
                                <label>Zakat mal (Rp)<input type="number" name="zakat_mal" min="0" step="1000" value="{{ old('zakat_mal', $receiptEditing->zakat_mal ?? 0) }}"></label>
                                <label>Fidyah (hari)<input type="number" name="fidyah_days" min="0" value="{{ old('fidyah_days', $receiptEditing->fidyah_days ?? 0) }}"></label>
                                <label>Fidyah beras (kg)<input type="number" name="fidyah_rice_kg" min="0" step="0.01" value="{{ old('fidyah_rice_kg', $receiptEditing->fidyah_rice_kg ?? 0) }}"></label>
                                <label>Fidyah uang (Rp)<input type="number" name="fidyah_money" min="0" step="1000" value="{{ old('fidyah_money', $receiptEditing->fidyah_money ?? 0) }}"></label>
                                <label>Infaq (Rp)<input type="number" name="infaq" min="0" step="1000" value="{{ old('infaq', $receiptEditing->infaq ?? 0) }}"></label>
                                <label>Shodaqoh (Rp)<input type="number" name="shodaqoh" min="0" step="1000" value="{{ old('shodaqoh', $receiptEditing->shodaqoh ?? 0) }}"></label>
                                <label>Petugas<input type="text" name="officer_name" value="{{ old('officer_name', $receiptEditing->officer_name ?? '') }}"></label>
                                <label class="operations-field--wide">Keterangan<textarea name="notes" rows="2">{{ old('notes', $receiptEditing->notes ?? '') }}</textarea></label>
                            </div>
                            <div class="operations-form__actions"><button class="button button--primary" type="submit">{{ $receiptEditing ? 'Simpan perubahan' : 'Simpan penerimaan' }}</button>@if($receiptEditing)<a class="button button--secondary" href="{{ route('zakat-admin.index', ['year' => $year, 'tab' => 'penerimaan']) }}">Batal</a>@endif</div>
                        </form>
                    @endif
                </section>
                <section class="operations-panel operations-panel--table"><div class="operations-panel__heading"><div><p class="eyebrow eyebrow--green">{{ $year }}</p><h3>Daftar penerimaan</h3></div><span class="operations-count">{{ $receipts->count() }} catatan</span></div>
                    <div class="overflow-x-auto"><table class="operations-table"><thead><tr><th>Tanggal</th><th>Muzakki</th><th>Jiwa</th><th>Beras</th><th>Fitrah uang</th><th>Mal</th><th>Fidyah</th><th>Infaq/shodaqoh</th><th>Aksi</th></tr></thead><tbody>
                        @forelse($receipts as $receipt)<tr><td>{{ $receipt->date->format('d/m/Y') }}</td><td>{{ $receipt->muzakki_name }}</td><td>{{ $receipt->souls }}</td><td>{{ number_format($receipt->fitrah_rice_kg + $receipt->fidyah_rice_kg, 2, ',', '.') }} kg</td><td>Rp {{ number_format($receipt->fitrah_money, 0, ',', '.') }}</td><td>Rp {{ number_format($receipt->zakat_mal, 0, ',', '.') }}</td><td>Rp {{ number_format($receipt->fidyah_money, 0, ',', '.') }}</td><td>Rp {{ number_format($receipt->infaq + $receipt->shodaqoh, 0, ',', '.') }}</td><td class="operations-actions"><a href="{{ route('zakat-admin.receipts.print', $receipt) }}">Tanda</a><a href="{{ route('zakat-admin.index', ['year' => $year, 'tab' => 'penerimaan', 'edit_receipt' => $receipt->id]) }}">Ubah</a><form method="POST" action="{{ route('zakat-admin.receipts.destroy', $receipt) }}" onsubmit="return confirm('Hapus penerimaan ini?')">@csrf @method('DELETE')<input type="hidden" name="year" value="{{ $year }}"><button>Hapus</button></form></td></tr>
                        @empty<tr><td colspan="9" class="operations-empty">Belum ada penerimaan pada periode ini.</td></tr>@endforelse
                    </tbody></table></div>
                </section>
            @elseif ($tab === 'distribusi')
                <section class="operations-panel">
                    <div class="operations-panel__heading"><div><p class="eyebrow eyebrow--green">{{ $year }}</p><h3>{{ $distributionEditing ? 'Ubah distribusi' : 'Catat distribusi zakat' }}</h3></div></div>
                    @if (!$period || $period->status === 'Ditutup')<p class="operations-empty">Buat atau aktifkan periode zakat di tab Periode sebelum mencatat distribusi.</p>@else
                        <form action="{{ $distributionEditing ? route('zakat-admin.distributions.update', $distributionEditing) : route('zakat-admin.distributions.store') }}" method="POST" class="operations-form">
                            @csrf @if ($distributionEditing) @method('PUT') @endif
                            <input type="hidden" name="year" value="{{ $year }}">
                            <div class="operations-fields">
                                <label>Tanggal<input type="date" name="date" value="{{ old('date', isset($distributionEditing->date) ? $distributionEditing->date->format('Y-m-d') : now()->format('Y-m-d')) }}" required></label>
                                <label>Nama penerima<input type="text" name="recipient_name" value="{{ old('recipient_name', $distributionEditing->recipient_name ?? '') }}" required></label>
                                <label>Alamat<input type="text" name="address" value="{{ old('address', $distributionEditing->address ?? '') }}"></label>
                                <label>Kategori asnaf<input type="text" name="asnaf" value="{{ old('asnaf', $distributionEditing->asnaf ?? '') }}" placeholder="Fakir / miskin / lainnya"></label>
                                <label>Fitrah beras (kg)<input type="number" name="fitrah_rice_kg" min="0" step="0.01" value="{{ old('fitrah_rice_kg', $distributionEditing->fitrah_rice_kg ?? 0) }}"></label>
                                <label>Fitrah uang (Rp)<input type="number" name="fitrah_money" min="0" step="1000" value="{{ old('fitrah_money', $distributionEditing->fitrah_money ?? 0) }}"></label>
                                <label>Zakat mal (Rp)<input type="number" name="zakat_mal" min="0" step="1000" value="{{ old('zakat_mal', $distributionEditing->zakat_mal ?? 0) }}"></label>
                                <label>Fidyah beras (kg)<input type="number" name="fidyah_rice_kg" min="0" step="0.01" value="{{ old('fidyah_rice_kg', $distributionEditing->fidyah_rice_kg ?? 0) }}"></label>
                                <label>Fidyah uang (Rp)<input type="number" name="fidyah_money" min="0" step="1000" value="{{ old('fidyah_money', $distributionEditing->fidyah_money ?? 0) }}"></label>
                                <label>Infaq (Rp)<input type="number" name="infaq" min="0" step="1000" value="{{ old('infaq', $distributionEditing->infaq ?? 0) }}"></label>
                                <label>Shodaqoh (Rp)<input type="number" name="shodaqoh" min="0" step="1000" value="{{ old('shodaqoh', $distributionEditing->shodaqoh ?? 0) }}"></label>
                                <label>Status<select name="status"><option value="Belum Disalurkan" @selected(old('status', $distributionEditing->status ?? 'Belum Disalurkan') === 'Belum Disalurkan')>Belum Disalurkan</option><option value="Sudah Disalurkan" @selected(old('status', $distributionEditing->status ?? '') === 'Sudah Disalurkan')>Sudah Disalurkan</option></select></label>
                                <label>Tanggal disalurkan<input type="date" name="distributed_at" value="{{ old('distributed_at', isset($distributionEditing->distributed_at) ? $distributionEditing->distributed_at->format('Y-m-d') : '') }}"></label>
                                <label class="operations-field--wide">Keterangan<textarea name="notes" rows="2">{{ old('notes', $distributionEditing->notes ?? '') }}</textarea></label>
                            </div>
                            <div class="operations-form__actions"><button class="button button--primary" type="submit">{{ $distributionEditing ? 'Simpan perubahan' : 'Simpan distribusi' }}</button>@if($distributionEditing)<a class="button button--secondary" href="{{ route('zakat-admin.index', ['year' => $year, 'tab' => 'distribusi']) }}">Batal</a>@endif</div>
                        </form>
                    @endif
                </section>
                <section class="operations-panel operations-panel--table"><div class="operations-panel__heading"><div><p class="eyebrow eyebrow--green">{{ $year }}</p><h3>Daftar distribusi</h3></div><span class="operations-count">{{ $distributions->count() }} penerima</span></div>
                    <div class="overflow-x-auto"><table class="operations-table"><thead><tr><th>Tanggal</th><th>Penerima</th><th>Asnaf</th><th>Beras</th><th>Uang</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
                        @forelse($distributions as $distribution)<tr><td>{{ $distribution->date->format('d/m/Y') }}</td><td>{{ $distribution->recipient_name }}</td><td>{{ $distribution->asnaf ?: '-' }}</td><td>{{ number_format($distribution->fitrah_rice_kg + $distribution->fidyah_rice_kg, 2, ',', '.') }} kg</td><td>Rp {{ number_format($distribution->fitrah_money + $distribution->zakat_mal + $distribution->fidyah_money + $distribution->infaq + $distribution->shodaqoh, 0, ',', '.') }}</td><td>{{ $distribution->status }}</td><td class="operations-actions"><a href="{{ route('zakat-admin.index', ['year' => $year, 'tab' => 'distribusi', 'edit_distribution' => $distribution->id]) }}">Ubah</a><form method="POST" action="{{ route('zakat-admin.distributions.destroy', $distribution) }}" onsubmit="return confirm('Hapus distribusi ini?')">@csrf @method('DELETE')<input type="hidden" name="year" value="{{ $year }}"><button>Hapus</button></form></td></tr>
                        @empty<tr><td colspan="7" class="operations-empty">Belum ada distribusi pada periode ini.</td></tr>@endforelse
                    </tbody></table></div>
                </section>
            @elseif ($tab === 'laporan')
                <section class="operations-panel">
                    <div class="operations-panel__heading"><div><p class="eyebrow eyebrow--green">{{ $year }}{{ $period?->hijri_year ? ' · ' . $period->hijri_year . ' H' : '' }}</p><h3>Hasil akhir zakat</h3></div></div>
                    @if (!$period)
                        <p class="operations-empty">Buat periode terlebih dahulu sebelum menghasilkan laporan.</p>
                    @else
                        <p class="text-sm text-gray-600">Preview mengikuti susunan dokumen <strong>HASIL AKHIR ZAKAT FITRAH.docx</strong> dan mengambil semua nilai dari transaksi periode ini.</p>
                        <a class="button button--primary mt-4 inline-flex" href="{{ route('zakat-admin.report.print', $period) }}" target="_blank">Preview / Cetak laporan</a>
                    @endif
                    <div class="mt-6 overflow-x-auto"><table class="operations-table"><thead><tr><th>Waktu dibuat</th><th>Jenis</th><th>Pengguna</th><th>Aksi</th></tr></thead><tbody>
                        @forelse($reports as $report)<tr><td>{{ $report->created_at->format('d/m/Y H:i') }}</td><td>{{ $report->report_type }}</td><td>{{ $report->generated_by ?: '-' }}</td><td><a href="{{ route('zakat-admin.report.archive.print', $report) }}" target="_blank">Cetak ulang</a></td></tr>@empty<tr><td colspan="4" class="operations-empty">Belum ada laporan yang diarsipkan.</td></tr>@endforelse
                    </tbody></table></div>
                </section>
            @elseif ($tab === 'pengaturan')
                <section class="operations-panel">
                    <div class="operations-panel__heading"><div><p class="eyebrow eyebrow--green">Identitas diambil dari pengaturan</p><h3>Identitas masjid</h3></div></div>
                    <form method="POST" action="{{ route('zakat-admin.settings.update') }}" class="operations-form">
                        @csrf
                        <div class="operations-fields">
                            <label>Nama masjid<input name="mosque_name" value="{{ old('mosque_name', $settings['mosque_name'] ?? '') }}" required></label>
                            <label>Alamat masjid<input name="mosque_address" value="{{ old('mosque_address', $settings['mosque_address'] ?? '') }}" required></label>
                            <label>Alamat sekretariat<input name="secretariat_address" value="{{ old('secretariat_address', $settings['secretariat_address'] ?? '') }}" required></label>
                            <label>Telepon<input name="mosque_phone" value="{{ old('mosque_phone', $settings['mosque_phone'] ?? '') }}"></label>
                            <label>Kop surat<select name="letterhead_asset"><option value="MASJID/KOP FIX.png" @selected(($settings['letterhead_asset'] ?? '') === 'MASJID/KOP FIX.png')>KOP FIX.png</option><option value="MASJID/KOP.png" @selected(($settings['letterhead_asset'] ?? '') === 'MASJID/KOP.png')>KOP.png</option></select></label>
                            <label>Cap/stempel<select name="stamp_asset"><option value="MASJID/cap_msjid_fix-removebg-preview.png" @selected(($settings['stamp_asset'] ?? '') === 'MASJID/cap_msjid_fix-removebg-preview.png')>Cap transparan</option><option value="MASJID/cap masjid.jpeg" @selected(($settings['stamp_asset'] ?? '') === 'MASJID/cap masjid.jpeg')>Cap scan</option></select></label>
                        </div>
                        <p class="operations-privacy">KOP FIX dan KOP.png berbeda pada nomor alamat sekretariat; periksa kop yang dipilih sebelum mengaktifkan template cetak.</p>
                        <div class="operations-form__actions"><button class="button button--primary">Simpan identitas</button></div>
                    </form>
                </section>
                <section class="operations-panel">
                    <div class="operations-panel__heading"><div><p class="eyebrow eyebrow--green">Pengurus</p><h3>Penandatangan</h3></div></div>
                    <form method="POST" action="{{ route('zakat-admin.officials.store') }}" class="operations-form">
                        @csrf
                        <div class="operations-fields"><label>Nama<input name="name" required></label><label>Gelar<input name="title"></label><label>Jabatan<input name="position" required></label><label>Urutan<input type="number" name="sort_order" min="0" value="0"></label><label class="operations-check"><input type="checkbox" name="is_signatory" value="1"> Dapat menandatangani surat</label></div>
                        <div class="operations-form__actions"><button class="button button--primary">Tambah pengurus</button></div>
                    </form>
                    <div class="mt-4 overflow-x-auto"><table class="operations-table"><thead><tr><th>Nama</th><th>Jabatan</th><th>Penandatangan</th></tr></thead><tbody>@forelse($officials as $official)<tr><td>{{ $official->name }} {{ $official->title }}</td><td>{{ $official->position }}</td><td>{{ $official->is_signatory ? 'Ya' : 'Tidak' }}</td></tr>@empty<tr><td colspan="3" class="operations-empty">Belum ada pengurus yang dimasukkan.</td></tr>@endforelse</tbody></table></div>
                </section>
                <section class="operations-panel">
                    <div class="operations-panel__heading"><div><p class="eyebrow eyebrow--green">Sumber: MASJID.zip</p><h3>Versi template surat</h3></div></div>
                    <p class="operations-privacy mb-4">Template yang belum dibandingkan dengan hasil cetak sumber tetap nonaktif. Periksa margin, field, kode nomor, kop, dan tanda tangan sebelum menandai terverifikasi.</p>
                    <div class="space-y-4">
                        @foreach ($letterTemplates as $template)
                            <details class="template-review">
                                <summary>
                                    <span><strong>{{ $template->name }}</strong><small>{{ $template->category }} · v{{ $template->version }} · {{ $template->source_path }}</small></span>
                                    <span class="template-review__status">{{ $template->is_verified ? ($template->is_active ? 'Aktif / Terverifikasi' : 'Terverifikasi / Nonaktif') : 'Belum diverifikasi' }}</span>
                                </summary>
                                <form method="POST" action="{{ route('zakat-admin.templates.update', $template) }}" class="operations-form mt-4">
                                    @csrf @method('PATCH')
                                    <div class="operations-fields">
                                        <label>Lebar halaman (mm)<input type="number" name="page_width_mm" min="100" step="0.01" value="{{ $template->page_width_mm }}" required></label>
                                        <label>Tinggi halaman (mm)<input type="number" name="page_height_mm" min="100" step="0.01" value="{{ $template->page_height_mm }}" required></label>
                                        <label>Orientasi<select name="orientation"><option value="portrait" @selected($template->orientation === 'portrait')>Portrait</option><option value="landscape" @selected($template->orientation === 'landscape')>Landscape</option></select></label>
                                        <label>Margin atas (mm)<input type="number" name="margin_top_mm" min="0" step="0.01" value="{{ $template->margins_mm['top'] ?? 0 }}" required></label>
                                        <label>Margin kanan (mm)<input type="number" name="margin_right_mm" min="0" step="0.01" value="{{ $template->margins_mm['right'] ?? 0 }}" required></label>
                                        <label>Margin bawah (mm)<input type="number" name="margin_bottom_mm" min="0" step="0.01" value="{{ $template->margins_mm['bottom'] ?? 0 }}" required></label>
                                        <label>Margin kiri (mm)<input type="number" name="margin_left_mm" min="0" step="0.01" value="{{ $template->margins_mm['left'] ?? 0 }}" required></label>
                                        <label>Kelompok nomor<input name="numbering_key" value="{{ $template->numbering_key }}" placeholder="Pan-Ramadhan / Idul-Adha"></label>
                                        <label>Format nomor<input name="numbering_pattern" value="{{ $template->numbering_pattern }}" placeholder="{seq}/{month_roman}/.../{year_short}"></label>
                                        <label class="operations-field--wide">Field dinamis (JSON)<textarea name="fields_json" rows="4" required>{{ json_encode($template->fields, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</textarea></label>
                                        <label class="operations-check"><input type="hidden" name="is_verified" value="0"><input type="checkbox" name="is_verified" value="1" @checked($template->is_verified)> Sudah dicocokkan dengan sumber</label>
                                        <label class="operations-check"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked($template->is_active)> Aktif untuk pembuatan surat</label>
                                    </div>
                                    <div class="operations-form__actions"><button class="button button--primary">Simpan versi template</button></div>
                                </form>
                            </details>
                        @endforeach
                    </div>
                </section>
            @elseif ($tab === 'ringkasan')
                @if (!$period)
                    <section class="operations-panel"><div class="operations-panel__heading"><div><p class="eyebrow eyebrow--green">{{ $year }}</p><h3>Periode zakat belum disiapkan</h3></div></div><p class="text-sm text-gray-600">Tetapkan tahun Hijriah, rentang pelayanan, berat fitrah, dan nilai fidyah dari ketentuan Takmir sebelum mencatat transaksi.</p><a class="button button--primary mt-4 inline-flex" href="{{ route('zakat-admin.index', ['year' => $year, 'tab' => 'periode']) }}">Siapkan periode</a></section>
                @else
                    <div class="operations-summary">
                        <div><span>Jiwa</span><strong>{{ number_format($summary['receipts']['souls'], 0, ',', '.') }}</strong></div>
                        <div><span>Fitrah beras masuk</span><strong>{{ number_format($summary['receipts']['fitrah_rice_kg'], 2, ',', '.') }} kg</strong></div>
                        <div><span>Fitrah uang masuk</span><strong>Rp {{ number_format($summary['receipts']['fitrah_money'], 0, ',', '.') }}</strong></div>
                        <div><span>Zakat mal masuk</span><strong>Rp {{ number_format($summary['receipts']['zakat_mal'], 0, ',', '.') }}</strong></div>
                        <div><span>Fidyah masuk</span><strong>{{ number_format($summary['receipts']['fidyah_rice_kg'], 2, ',', '.') }} kg · Rp {{ number_format($summary['receipts']['fidyah_money'], 0, ',', '.') }}</strong></div>
                        <div><span>Infaq & shodaqoh</span><strong>Rp {{ number_format($summary['receipts']['infaq'] + $summary['receipts']['shodaqoh'], 0, ',', '.') }}</strong></div>
                    </div>
                    <section class="operations-panel operations-panel--table"><div class="operations-panel__heading"><div><p class="eyebrow eyebrow--green">Penyaluran berhasil</p><h3>Sisa dana dan beras</h3></div><span class="operations-count">{{ $summary['distributions']['recipients'] }} penerima</span></div>
                        <div class="overflow-x-auto"><table class="operations-table"><thead><tr><th>Jenis</th><th>Diterima</th><th>Disalurkan</th><th>Sisa</th></tr></thead><tbody>
                            @foreach ([['Fitrah beras', 'fitrah_rice_kg', ' kg', 2], ['Fitrah uang', 'fitrah_money', '', 0], ['Zakat mal', 'zakat_mal', '', 0], ['Fidyah beras', 'fidyah_rice_kg', ' kg', 2], ['Fidyah uang', 'fidyah_money', '', 0]] as [$label, $key, $unit, $precision])
                                <tr><td>{{ $label }}</td><td>{{ $unit ? number_format($summary['receipts'][$key], $precision, ',', '.') . $unit : 'Rp ' . number_format($summary['receipts'][$key], 0, ',', '.') }}</td><td>{{ $unit ? number_format($summary['distributions'][$key], $precision, ',', '.') . $unit : 'Rp ' . number_format($summary['distributions'][$key], 0, ',', '.') }}</td><td class="{{ $summary['remaining'][$key] < 0 ? 'text-red-700 font-bold' : '' }}">{{ $unit ? number_format($summary['remaining'][$key], $precision, ',', '.') . $unit : 'Rp ' . number_format($summary['remaining'][$key], 0, ',', '.') }}</td></tr>
                            @endforeach
                        </tbody></table></div>
                    </section>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>