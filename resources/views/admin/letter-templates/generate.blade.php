<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-emerald-700">Generate surat</p>
                <h2 class="mt-1 text-2xl font-semibold leading-tight text-gray-900">{{ $template->name }}</h2>
            </div>
            <a href="{{ route('letter-template.index') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-emerald-800 hover:text-emerald-600">
                <span aria-hidden="true">&larr;</span> Kembali
            </a>
        </div>
    </x-slot>

    <div class="dashboard-workspace min-h-screen px-0 py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <section class="operations-panel">
                <div class="operations-panel__heading">
                    <div>
                        <p class="eyebrow eyebrow--green">Isi data</p>
                        <h3>Generate DOCX berdasarkan template</h3>
                    </div>
                </div>

                <form action="{{ route('letter-template.generate', $template) }}" method="POST" class="operations-form">
                    @csrf
                    <div class="operations-fields">
                        @foreach ($fields as $field)
                            <label>{{ str_replace('_', ' ', ucfirst($field)) }}
                                <input type="text" name="{{ $field }}" value="{{ old($field, $sampleValues[$field] ?? '') }}" placeholder="{{ $field }}">
                            </label>
                        @endforeach
                        <label class="operations-field--wide">Isi surat / body <textarea name="body" rows="8" placeholder="Contoh: Nomor: @{{nomor_surat}} ...">{{ old('body', $defaultBody) }}</textarea></label>
                    </div>

                    <div class="operations-form__actions">
                        <button type="submit" class="button button--primary">Generate DOCX</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
