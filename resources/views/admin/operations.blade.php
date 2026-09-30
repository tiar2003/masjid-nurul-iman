<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-emerald-700">Satu pintu untuk kerja harian</p>
                <h2 class="mt-1 text-2xl font-semibold leading-tight text-gray-900">Administrasi Masjid</h2>
                <p class="mt-1 text-sm text-gray-500">Kelola kegiatan, Qurban, santunan, dan surat tanpa mengedit file Word atau Excel.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-emerald-800 hover:text-emerald-600">
                <span aria-hidden="true">&larr;</span> Dashboard
            </a>
        </div>
    </x-slot>

        @php
            $defaultDate = $year === now()->year ? now()->format('Y-m-d') : sprintf('%04d-01-01', $year);
        @endphp

    <div class="dashboard-workspace min-h-screen px-0 py-8" x-data="{
        theme: localStorage.getItem('masjidAdminTheme') || 'light',
        init() { document.documentElement.dataset.adminTheme = this.theme; },
        setTheme(theme) { this.theme = theme; localStorage.setItem('masjidAdminTheme', theme); document.documentElement.dataset.adminTheme = theme; }
    }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="dashboard-toolbar">
                <div class="dashboard-toolbar__status">
                    <span class="dashboard-toolbar__dot"></span>
                    <span>Administrasi Online</span>
                    <span class="dashboard-toolbar__separator">/</span>
                    <span>{{ $moduleLabel }} · {{ $year }}</span>
                </div>
                <div class="dashboard-theme" role="group" aria-label="Tema tampilan">
                    <span class="dashboard-theme__label">Tema</span>
                    <button type="button" @click="setTheme('light')" :aria-pressed="(theme === 'light').toString()" :class="{'is-active': theme === 'light'}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg>
                        Terang
                    </button>
                    <button type="button" @click="setTheme('dark')" :aria-pressed="(theme === 'dark').toString()" :class="{'is-active': theme === 'dark'}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.9 13A9 9 0 0 1 11 3.1 9 9 0 1 0 20.9 13Z"/></svg>
                        Gelap
                    </button>
                </div>
            </div>

            <form action="{{ route('operations.index') }}" method="GET" class="operations-year-filter">
                <input type="hidden" name="module" value="{{ $module }}">
                <label for="operations-year">Tahun kerja</label>
                <select id="operations-year" name="year" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                    @foreach ($years as $availableYear)
                        <option value="{{ $availableYear }}" @selected($year === $availableYear)>{{ $availableYear }}</option>
                    @endforeach
                </select>
                <label for="operations-search">Cari</label>
                <input id="operations-search" type="search" name="q" value="{{ $search }}" placeholder="Nomor, nama, perihal..." class="rounded-md border-gray-300 text-sm">
                <button class="rounded-md bg-emerald-700 px-3 py-2 text-xs font-bold text-white">Cari</button>
                @if ($module === 'letters')
                    <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="archived" value="1" @checked($showArchived)> Arsip</label>
                @endif
                <noscript><button class="rounded-md bg-emerald-700 px-3 py-2 text-xs font-bold text-white">Tampilkan</button></noscript>
            </form>

            <nav class="operations-tabs" aria-label="Modul administrasi">
                @foreach ($modules as $key => $label)
                    <a href="{{ route('operations.index', ['module' => $key, 'year' => $year]) }}" @if ($module === $key) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </nav>

            @if (session('success'))
                <div class="dashboard-feedback dashboard-feedback--success" role="status">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="dashboard-feedback" role="alert">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="dashboard-feedback" role="alert">
                    <strong>Periksa kembali data yang dimasukkan.</strong>
                    <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            @if ($module === 'qurban-finance')
                <div class="operations-summary">
                    <div><span>Pemasukan</span><strong>Rp {{ number_format($qurbanIncome, 0, ',', '.') }}</strong></div>
                    <div><span>Pengeluaran</span><strong>Rp {{ number_format($qurbanExpense, 0, ',', '.') }}</strong></div>
                    <div><span>Saldo</span><strong>Rp {{ number_format($qurbanIncome - $qurbanExpense, 0, ',', '.') }}</strong></div>
                </div>
            @elseif ($module === 'charity-donations' || $module === 'charity-distributions')
                <div class="operations-summary">
                    <div><span>Donasi terkumpul</span><strong>Rp {{ number_format($charityDonations, 0, ',', '.') }}</strong></div>
                    <div><span>Sudah disalurkan</span><strong>Rp {{ number_format($charityDistributions, 0, ',', '.') }}</strong></div>
                    <div><span>Sisa amanah</span><strong>Rp {{ number_format($charityDonations - $charityDistributions, 0, ',', '.') }}</strong></div>
                </div>
            @endif

            <section class="operations-panel">
                <div class="operations-panel__heading">
                    <div>
                        <p class="eyebrow eyebrow--green">{{ $isEditing ? 'Perbarui catatan' : 'Formulir website' }}</p>
                        <h3>{{ $isEditing ? 'Ubah ' . $moduleLabel : 'Tambah ' . $moduleLabel }}</h3>
                    </div>
                    @if ($isEditing)
                        <a class="text-sm font-semibold text-emerald-800" href="{{ route('operations.index', ['module' => $module, 'year' => $year]) }}">Batal mengubah</a>
                    @endif
                </div>

                <form action="{{ $formAction }}" method="POST" class="operations-form">
                    @csrf
                    @if ($isEditing) @method('PUT') @endif

                    @if ($module === 'ramadan')
                        <div class="operations-fields">
                            <label>Jenis kegiatan
                                <select name="kind" required><option value="Kultum" @selected(old('kind', $editing->kind ?? '') === 'Kultum')>Kultum</option><option value="Takjil" @selected(old('kind', $editing->kind ?? '') === 'Takjil')>Takjil</option></select>
                            </label>
                            <label>Tanggal<input type="date" name="date" value="{{ old('date', isset($editing->date) ? $editing->date->format('Y-m-d') : '') }}" required></label>
                            <label>{{ old('kind', $editing->kind ?? 'Kultum') === 'Takjil' ? 'Nama penyedia / penanggung jawab' : 'Nama penceramah' }}<input type="text" name="person_name" value="{{ old('person_name', $editing->person_name ?? '') }}" maxlength="255"></label>
                            <label>{{ old('kind', $editing->kind ?? 'Kultum') === 'Takjil' ? 'Menu makanan' : 'Tema kultum' }}<input type="text" name="title" value="{{ old('title', $editing->title ?? '') }}" maxlength="255"></label>
                            <label>Jumlah porsi (khusus takjil)<input type="number" name="quantity" min="1" value="{{ old('quantity', $editing->quantity ?? '') }}"></label>
                            <label class="operations-field--wide">Catatan<textarea name="notes" rows="2">{{ old('notes', $editing->notes ?? '') }}</textarea></label>
                            <label class="operations-check operations-field--wide"><input type="checkbox" name="is_public" value="1" @checked(old('is_public', $editing->is_public ?? true))> Tampilkan pada website publik</label>
                        </div>
                    @elseif ($module === 'qurban-team')
                        <div class="operations-fields">
                            <label>Tahun<input type="number" name="year" min="2000" max="2100" value="{{ old('year', $editing->year ?? $year) }}" required></label>
                            <label>Nama panitia<input type="text" name="name" value="{{ old('name', $editing->name ?? '') }}" required maxlength="255"></label>
                            <label>Jabatan / tugas<input type="text" name="position" value="{{ old('position', $editing->position ?? '') }}" required maxlength="255"></label>
                            <label>Nomor kontak (privat)<input type="text" name="phone" value="{{ old('phone', $editing->phone ?? '') }}" maxlength="30"></label>
                            <label class="operations-field--wide">Catatan<textarea name="notes" rows="2">{{ old('notes', $editing->notes ?? '') }}</textarea></label>
                        </div>
                    @elseif ($module === 'qurban-contributions')
                        <div class="operations-fields">
                            <label>Tahun<input type="number" name="year" min="2000" max="2100" value="{{ old('year', $editing->year ?? $year) }}" required></label>
                            <label>Jenis hewan<select name="animal_type" required><option value="Sapi" @selected(old('animal_type', $editing->animal_type ?? '') === 'Sapi')>Sapi</option><option value="Kambing" @selected(old('animal_type', $editing->animal_type ?? '') === 'Kambing')>Kambing</option></select></label>
                            <label>Kelompok / nomor hewan<input type="text" name="animal_group" value="{{ old('animal_group', $editing->animal_group ?? '') }}" placeholder="Contoh: Sapi 1"></label>
                            <label>Nama peserta<input type="text" name="participant_name" value="{{ old('participant_name', $editing->participant_name ?? '') }}" required></label>
                            <label>Nilai iuran (Rp)<input type="number" name="amount" min="0" step="1000" value="{{ old('amount', $editing->amount ?? 0) }}" required></label>
                            <label>Sudah dibayar (Rp)<input type="number" name="paid_amount" min="0" step="1000" value="{{ old('paid_amount', $editing->paid_amount ?? 0) }}" required></label>
                            <label>Tanggal bayar<input type="date" name="paid_at" value="{{ old('paid_at', isset($editing->paid_at) ? $editing->paid_at->format('Y-m-d') : '') }}"></label>
                            <label class="operations-field--wide">Catatan<textarea name="notes" rows="2">{{ old('notes', $editing->notes ?? '') }}</textarea></label>
                        </div>
                    @elseif ($module === 'qurban-finance')
                        <div class="operations-fields">
                            <label>Tahun<input type="number" name="year" min="2000" max="2100" value="{{ old('year', $editing->year ?? $year) }}" required></label>
                            <label>Jenis transaksi<select name="transaction_type" required><option value="Pemasukan" @selected(old('transaction_type', $editing->transaction_type ?? '') === 'Pemasukan')>Pemasukan</option><option value="Pengeluaran" @selected(old('transaction_type', $editing->transaction_type ?? '') === 'Pengeluaran')>Pengeluaran</option></select></label>
                            <label>Tanggal<input type="date" name="date" value="{{ old('date', isset($editing->date) ? $editing->date->format('Y-m-d') : $defaultDate) }}" required></label>
                            <label>Kategori<input type="text" name="category" value="{{ old('category', $editing->category ?? '') }}" placeholder="Hewan, konsumsi, perlengkapan" required></label>
                            <label>Uraian<input type="text" name="description" value="{{ old('description', $editing->description ?? '') }}" required></label>
                            <label>Nama pihak terkait<input type="text" name="party_name" value="{{ old('party_name', $editing->party_name ?? '') }}"></label>
                            <label>Nominal (Rp)<input type="number" name="amount" min="1" step="1000" value="{{ old('amount', $editing->amount ?? '') }}" required></label>
                            <label class="operations-field--wide">Catatan<textarea name="notes" rows="2">{{ old('notes', $editing->notes ?? '') }}</textarea></label>
                        </div>
                    @elseif ($module === 'charity-beneficiaries')
                        <div class="operations-fields">
                            <label>Tahun santunan<input type="number" name="year" min="2000" max="2100" value="{{ old('year', $editing->year ?? $year) }}" required></label>
                            <label>Nama penerima<input type="text" name="name" value="{{ old('name', $editing->name ?? '') }}" required></label>
                            <label>Nama wali (privat)<input type="text" name="guardian_name" value="{{ old('guardian_name', $editing->guardian_name ?? '') }}"></label>
                            <label>Kelompok / wilayah<input type="text" name="group_name" value="{{ old('group_name', $editing->group_name ?? '') }}"></label>
                            <label class="operations-field--wide">Catatan privat<textarea name="private_notes" rows="2">{{ old('private_notes', $editing->private_notes ?? '') }}</textarea></label>
                            <label class="operations-check operations-field--wide"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $editing->is_active ?? true))> Masih aktif menerima santunan</label>
                        </div>
                    @elseif ($module === 'charity-donations')
                        <div class="operations-fields">
                            <label>Tahun santunan<input type="number" name="year" min="2000" max="2100" value="{{ old('year', $editing->year ?? $year) }}" required></label>
                            <label>Tanggal diterima<input type="date" name="date" value="{{ old('date', isset($editing->date) ? $editing->date->format('Y-m-d') : $defaultDate) }}" required></label>
                            <label>Nama donatur (privat)<input type="text" name="donor_name" value="{{ old('donor_name', $editing->donor_name ?? '') }}" required></label>
                            <label>Nominal (Rp)<input type="number" name="amount" min="1" step="1000" value="{{ old('amount', $editing->amount ?? '') }}" required></label>
                            <label>Metode pembayaran<input type="text" name="payment_method" value="{{ old('payment_method', $editing->payment_method ?? '') }}" placeholder="Tunai / transfer"></label>
                            <label class="operations-field--wide">Catatan privat<textarea name="private_notes" rows="2">{{ old('private_notes', $editing->private_notes ?? '') }}</textarea></label>
                        </div>
                    @elseif ($module === 'charity-distributions')
                        <div class="operations-fields">
                            <label>Tahun santunan<input type="number" name="year" min="2000" max="2100" value="{{ old('year', $editing->year ?? $year) }}" required></label>
                            <label>Penerima<select name="recipient_id" required><option value="">Pilih penerima</option>@foreach ($records['charity-beneficiaries'] as $recipient)<option value="{{ $recipient->id }}" @selected((int) old('recipient_id', $editing->recipient_id ?? 0) === $recipient->id)>{{ $recipient->name }}</option>@endforeach</select></label>
                            <label>Tanggal penyaluran<input type="date" name="date" value="{{ old('date', isset($editing->date) ? $editing->date->format('Y-m-d') : $defaultDate) }}" required></label>
                            <label>Jenis bantuan<input type="text" name="description" value="{{ old('description', $editing->description ?? '') }}" placeholder="Uang tunai, paket sembako" required></label>
                            <label>Nilai bantuan (Rp)<input type="number" name="amount" min="0" step="1000" value="{{ old('amount', $editing->amount ?? 0) }}" required></label>
                            <label class="operations-field--wide">Catatan privat<textarea name="private_notes" rows="2">{{ old('private_notes', $editing->private_notes ?? '') }}</textarea></label>
                        </div>
                    @else
                        <div class="operations-fields">
                            <label>Tanggal surat<input type="date" name="issue_date" value="{{ old('issue_date', isset($editing->issue_date) ? $editing->issue_date->format('Y-m-d') : $defaultDate) }}" required></label>
                            <label>Jenis surat<input type="text" name="letter_type" value="{{ old('letter_type', $editing->letter_type ?? '') }}" placeholder="Undangan, keterangan, permohonan" required></label>
                            <label>Kode nomor surat<input type="text" name="letter_code" value="{{ old('letter_code', $editing->letter_code ?? 'MNI') }}" placeholder="MNI, PHBI, PAN-RAMADHAN" required></label>
                            <label>Tujuan surat<input type="text" name="recipient" value="{{ old('recipient', $editing->recipient ?? '') }}" required></label>
                            <label>Perihal<input type="text" name="subject" value="{{ old('subject', $editing->subject ?? '') }}" required></label>
                            <label>Status<select name="status"><option value="Draft" @selected(old('status', $editing->status ?? 'Draft') === 'Draft')>Draft</option><option value="Terbit" @selected(old('status', $editing->status ?? '') === 'Terbit')>Terbit</option></select></label>
                            <label class="operations-field--wide">Isi surat<textarea name="body" rows="9" required>{{ old('body', $editing->body ?? '') }}</textarea></label>
                        </div>
                    @endif

                    <div class="operations-form__actions">
                        <button type="submit" class="button button--primary">{{ $isEditing ? 'Simpan perubahan' : 'Simpan catatan' }}</button>
                        @if ($module === 'letters' && $isEditing)
                            <a class="button button--secondary" href="{{ route('operations.letters.print', $editing) }}" target="_blank">Pratinjau / cetak surat</a>
                        @endif
                    </div>
                </form>
            </section>

            @if ($module === 'qurban-finance')
                <section class="operations-panel operations-panel--table">
                    <div class="operations-panel__heading"><div><p class="eyebrow eyebrow--green">{{ $year }}</p><h3>Buku kas Qurban</h3></div><button type="button" class="operations-print" onclick="window.print()">Cetak laporan</button></div>
                    <div class="overflow-x-auto">
                        <table class="operations-table"><thead><tr><th>Tanggal</th><th>Arus</th><th>Kategori</th><th>Uraian</th><th>Nominal</th><th>Aksi</th></tr></thead><tbody>
                            @forelse ($records[$module] as $item)
                                <tr><td>{{ $item->date->format('d/m/Y') }}</td><td><span class="operations-badge">{{ $item->transaction_type }}</span></td><td>{{ $item->category }}</td><td>{{ $item->description }}</td><td>Rp {{ number_format($item->amount, 0, ',', '.') }}</td><td>@include('admin.operations-row-actions', ['item' => $item])</td></tr>
                            @empty<tr><td colspan="6" class="operations-empty">Belum ada transaksi untuk tahun ini.</td></tr>@endforelse
                        </tbody></table>
                    </div>
                </section>
            @else
                <section class="operations-panel operations-panel--table">
                    <div class="operations-panel__heading"><div><p class="eyebrow eyebrow--green">{{ $year }}</p><h3>Daftar {{ $moduleLabel }}</h3></div><div class="operations-table-tools"><span class="operations-count">{{ $records[$module]->count() }} catatan</span><button type="button" class="operations-print" onclick="window.print()">Cetak laporan</button></div></div>
                    <div class="overflow-x-auto">
                        <table class="operations-table">
                            @if ($module === 'ramadan')
                                <thead><tr><th>Tanggal</th><th>Jenis</th><th>Penceramah / penyedia</th><th>Tema / menu</th><th>Jumlah</th><th>Publik</th><th>Aksi</th></tr></thead><tbody>
                                    @forelse ($records[$module] as $item)<tr><td>{{ $item->date->format('d/m/Y') }}</td><td>{{ $item->kind }}</td><td>{{ $item->person_name ?: '-' }}</td><td>{{ $item->title ?: '-' }}</td><td>{{ $item->quantity ?: '-' }}</td><td>{{ $item->is_public ? 'Ya' : 'Tidak' }}</td><td>@include('admin.operations-row-actions', ['item' => $item])</td></tr>@empty<tr><td colspan="7" class="operations-empty">Belum ada jadwal Ramadan.</td></tr>@endforelse
                                </tbody>
                            @elseif ($module === 'qurban-team')
                                <thead><tr><th>Nama</th><th>Jabatan / tugas</th><th>Kontak privat</th><th>Catatan</th><th>Aksi</th></tr></thead><tbody>
                                    @forelse ($records[$module] as $item)<tr><td>{{ $item->name }}</td><td>{{ $item->position }}</td><td>{{ $item->phone ?: '-' }}</td><td>{{ $item->notes ?: '-' }}</td><td>@include('admin.operations-row-actions', ['item' => $item])</td></tr>@empty<tr><td colspan="5" class="operations-empty">Daftar panitia belum diisi.</td></tr>@endforelse
                                </tbody>
                            @elseif ($module === 'qurban-contributions')
                                <thead><tr><th>Hewan / kelompok</th><th>Peserta</th><th>Iuran</th><th>Dibayar</th><th>Sisa</th><th>Aksi</th></tr></thead><tbody>
                                    @forelse ($records[$module] as $item)<tr><td>{{ $item->animal_type }} {{ $item->animal_group }}</td><td>{{ $item->participant_name }}</td><td>Rp {{ number_format($item->amount, 0, ',', '.') }}</td><td>Rp {{ number_format($item->paid_amount, 0, ',', '.') }}</td><td>Rp {{ number_format($item->amount - $item->paid_amount, 0, ',', '.') }}</td><td>@include('admin.operations-row-actions', ['item' => $item])</td></tr>@empty<tr><td colspan="6" class="operations-empty">Peserta Qurban belum dicatat.</td></tr>@endforelse
                                </tbody>
                            @elseif ($module === 'charity-beneficiaries')
                                <thead><tr><th>Nama penerima</th><th>Wali</th><th>Kelompok / wilayah</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
                                    @forelse ($records[$module] as $item)<tr><td>{{ $item->name }}</td><td>{{ $item->guardian_name ?: '-' }}</td><td>{{ $item->group_name ?: '-' }}</td><td>{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</td><td>@include('admin.operations-row-actions', ['item' => $item])</td></tr>@empty<tr><td colspan="5" class="operations-empty">Belum ada penerima terdaftar.</td></tr>@endforelse
                                </tbody>
                            @elseif ($module === 'charity-donations')
                                <thead><tr><th>Tanggal</th><th>Donatur</th><th>Metode</th><th>Nominal</th><th>Aksi</th></tr></thead><tbody>
                                    @forelse ($records[$module] as $item)<tr><td>{{ $item->date->format('d/m/Y') }}</td><td>{{ $item->donor_name }}</td><td>{{ $item->payment_method ?: '-' }}</td><td>Rp {{ number_format($item->amount, 0, ',', '.') }}</td><td>@include('admin.operations-row-actions', ['item' => $item])</td></tr>@empty<tr><td colspan="5" class="operations-empty">Belum ada donasi santunan.</td></tr>@endforelse
                                </tbody>
                            @elseif ($module === 'charity-distributions')
                                <thead><tr><th>Tanggal</th><th>Penerima</th><th>Bantuan</th><th>Nilai</th><th>Aksi</th></tr></thead><tbody>
                                    @forelse ($records[$module] as $item)<tr><td>{{ $item->date->format('d/m/Y') }}</td><td>{{ $item->recipient->name }}</td><td>{{ $item->description }}</td><td>Rp {{ number_format($item->amount, 0, ',', '.') }}</td><td>@include('admin.operations-row-actions', ['item' => $item])</td></tr>@empty<tr><td colspan="5" class="operations-empty">Belum ada penyaluran santunan.</td></tr>@endforelse
                                </tbody>
                            @else
                                <thead><tr><th>Nomor</th><th>Tanggal</th><th>Jenis</th><th>Tujuan</th><th>Perihal</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
                                    @forelse ($records[$module] as $item)
                                        <tr>
                                            <td class="font-semibold">{{ $item->letter_number }}</td>
                                            <td>{{ $item->issue_date->format('d/m/Y') }}</td>
                                            <td>{{ $item->letter_type }}</td>
                                            <td>{{ $item->recipient }}</td>
                                            <td>{{ $item->subject }}</td>
                                            <td>{{ $item->status }}</td>
                                            <td class="operations-actions">
                                                <a href="{{ route('operations.letters.print', $item) }}" target="_blank">Cetak</a>
                                                @if ($item->docx_path)
                                                    <a href="{{ route('operations.letters.docx', $item) }}">DOCX</a>
                                                @endif
                                                @if ($item->pdf_path)
                                                    <a href="{{ route('operations.letters.pdf', $item) }}">PDF</a>
                                                @endif
                                                @include('admin.operations-row-actions', ['item' => $item])
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="operations-empty">Belum ada surat untuk tahun ini.</td></tr>
                                    @endforelse
                                </tbody>
                            @endif
                        </table>
                    </div>
                    @if (in_array($module, ['charity-beneficiaries', 'charity-donations', 'charity-distributions'], true))
                        <p class="operations-privacy">Data penerima dan donatur hanya tampil di panel pengelola dan tidak dipublikasikan.</p>
                    @endif
                </section>
            @endif
        </div>
    </div>
</x-app-layout>