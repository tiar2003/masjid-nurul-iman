<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-emerald-700">Template surat</p>
                <h2 class="mt-1 text-2xl font-semibold leading-tight text-gray-900">Tambah template baru</h2>
            </div>
            <a href="{{ route('letter-template.index') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-emerald-800 hover:text-emerald-600">
                <span aria-hidden="true">&larr;</span> Kembali
            </a>
        </div>
    </x-slot>

    <div class="dashboard-workspace min-h-screen px-0 py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <section class="operations-panel">
                <div class="operations-panel__heading">
                    <div>
                        <p class="eyebrow eyebrow--green">Baru</p>
                        <h3>Form template surat</h3>
                    </div>
                </div>

                <form action="{{ route('letter-template.store') }}" method="POST" enctype="multipart/form-data" class="operations-form">
                    @csrf
                    <div class="operations-fields">
                        <label>Template key<input name="template_key" value="{{ old('template_key') }}" placeholder="SURAT_KEPUTUSAN" required></label>
                        <label>Nama template<input name="name" value="{{ old('name') }}" placeholder="Surat Pengantar" required></label>
                        <label>Kategori<input name="category" value="{{ old('category') }}" placeholder="Sekretariat" required></label>
                        <label>Versi<input type="number" name="version" min="1" value="{{ old('version', 1) }}"></label>
                        <label>Source path<input name="source_path" value="{{ old('source_path') }}" placeholder="MASJID/.../template.docx"></label>
                        <label>File template asli (.docx)<input type="file" name="template_file" accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document"></label>
                        <label>Numbering key<input name="numbering_key" value="{{ old('numbering_key') }}" placeholder="MNI"></label>
                        <label>Numbering pattern<input name="numbering_pattern" value="{{ old('numbering_pattern') }}" placeholder="{seq3}/MNI/I/{month_roman}/{year}"></label>
                        <label class="operations-field--wide">Field template<textarea name="fields" rows="4" placeholder="nomor_surat, tanggal, nama_penerima, jabatan_penerima, perihal" required>{{ old('fields') }}</textarea></label>
                        <label class="operations-check"><input type="checkbox" name="is_verified" value="1" @checked(old('is_verified'))> Sudah cocok dengan template sumber</label>
                        <label class="operations-check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active'))> Aktif untuk generate</label>
                    </div>
                    <div class="operations-form__actions">
                        <button type="submit" class="button button--primary">Simpan template</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
