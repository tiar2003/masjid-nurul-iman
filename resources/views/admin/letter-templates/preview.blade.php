<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-emerald-700">Preview template</p>
                <h2 class="mt-1 text-2xl font-semibold leading-tight text-gray-900">{{ $template->name }}</h2>
            </div>
            <a href="{{ route('letter-template.index') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-emerald-800 hover:text-emerald-600">
                <span aria-hidden="true">&larr;</span> Kembali
            </a>
        </div>
    </x-slot>

    <div class="dashboard-workspace min-h-screen px-0 py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <section class="operations-panel">
                <div class="operations-panel__heading">
                    <div>
                        <p class="eyebrow eyebrow--green">Field terdaftar</p>
                        <h3>Daftar placeholder</h3>
                    </div>
                </div>

                <div class="mb-5 flex flex-wrap gap-2 text-sm">
                    @foreach ($fields as $field)
                        @php($placeholder = '{{' . $field . '}}')
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-800">{{ $placeholder }}</span>
                    @endforeach
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <pre class="whitespace-pre-wrap text-sm text-slate-700">{{ $defaultBody }}</pre>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
