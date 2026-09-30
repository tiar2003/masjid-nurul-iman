<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-emerald-700">Sistem template surat</p>
                <h2 class="mt-1 text-2xl font-semibold leading-tight text-gray-900">Katalog Template Surat</h2>
                <p class="mt-1 text-sm text-gray-500">Audit sumber, versi, field, dan kesiapan template resmi dari MASJID.zip.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-emerald-800 hover:text-emerald-600">
                <span aria-hidden="true">&larr;</span> Dashboard
            </a>
        </div>
    </x-slot>

    <div class="dashboard-workspace min-h-screen px-0 py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="dashboard-feedback dashboard-feedback--success" role="status">{{ session('success') }}</div>
            @endif

            <div class="mb-6 flex justify-end">
                <a href="{{ route('letter-template.create') }}" class="button button--primary">Tambah template</a>
            </div>

            <form action="{{ route('letter-template.index') }}" method="GET" class="operations-year-filter mb-6">
                <label for="template-category">Kategori</label>
                <select id="template-category" name="category">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
                <label for="template-state">Status</label>
                <select id="template-state" name="state">
                    <option value="all" @selected(($filters['state'] ?? 'all') === 'all')>Semua</option>
                    <option value="ready" @selected(($filters['state'] ?? '') === 'ready')>Siap digunakan</option>
                    <option value="draft" @selected(($filters['state'] ?? '') === 'draft')>Belum siap</option>
                </select>
                <button class="rounded-md bg-emerald-700 px-3 py-2 text-xs font-bold text-white">Filter</button>
            </form>

            <section class="operations-panel">
                <div class="operations-panel__heading">
                    <div>
                        <p class="eyebrow eyebrow--green">Katalog template</p>
                        <h3>Daftar template dan versi ({{ $templates->count() }})</h3>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse ($templates as $template)
                        <div class="rounded-xl border border-emerald-100 bg-white p-4 shadow-sm">
                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">{{ $template->category }}</p>
                                    <h4 class="mt-1 text-lg font-bold text-gray-900">{{ $template->name }}</h4>
                                    <p class="text-sm text-gray-500">Versi {{ $template->version }} · {{ $template->source_path ?? 'Template terdaftar' }}</p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('letter-template.preview', $template) }}" class="button button--secondary">Preview</a>
                                    @if ($template->is_active && $template->is_verified)
                                        <a href="{{ route('letter-template.generate-form', $template) }}" class="button button--primary">Generate DOCX</a>
                                    @else
                                        <span class="button button--secondary">Belum siap generate</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3 flex flex-wrap gap-2 text-xs text-gray-600">
                                <span class="rounded-full bg-emerald-50 px-2 py-1">Key: {{ $template->template_key }}</span>
                                <span class="rounded-full bg-slate-100 px-2 py-1">{{ $template->is_verified ? 'Terverifikasi' : 'Belum diverifikasi' }}</span>
                                <span class="rounded-full bg-slate-100 px-2 py-1">{{ $template->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                <span class="rounded-full bg-slate-100 px-2 py-1">Field: {{ implode(', ', $template->fields ?: []) }}</span>
                                <span class="rounded-full {{ $template->source_available ? 'bg-emerald-50 text-emerald-800' : 'bg-red-50 text-red-800' }} px-2 py-1">{{ $template->source_available ? 'Sumber ditemukan' : 'Sumber hilang' }}</span>
                                <span class="rounded-full {{ $template->readiness_reason === 'Siap digunakan' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }} px-2 py-1">{{ $template->readiness_reason }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="operations-empty">Belum ada template surat yang dibuat.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
