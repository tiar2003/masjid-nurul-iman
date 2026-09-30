<?php

namespace App\Http\Controllers;

use App\Models\LetterTemplate;
use App\Models\MosqueLetter;
use App\Services\ArchiveTemplateResolver;
use App\Services\PdfDocumentGenerator;
use App\Services\WordDocumentGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class LetterTemplateController extends Controller
{
    public function __construct(private ArchiveTemplateResolver $templateResolver)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'category' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'in:all,ready,draft'],
        ]);
        $query = LetterTemplate::orderBy('category')->orderBy('name')->orderByDesc('version');
        if (! blank($filters['category'] ?? null)) {
            $query->where('category', $filters['category']);
        }
        if (($filters['state'] ?? 'all') === 'ready') {
            $query->where('is_active', true)->where('is_verified', true);
        } elseif (($filters['state'] ?? 'all') === 'draft') {
            $query->where(function ($query) {
                $query->where('is_active', false)->orWhere('is_verified', false);
            });
        }
        $templates = $query->get();
        $categories = LetterTemplate::query()->select('category')->distinct()->orderBy('category')->pluck('category');

        foreach ($templates as $template) {
            $resolved = $this->templateResolver->resolve($template->source_path);
            $template->source_available = $resolved !== null;
            if ($resolved && $resolved[1]) {
                @unlink($resolved[0]);
            }
            $template->readiness_reason = match (true) {
                ! $template->source_available => 'File sumber tidak ditemukan',
                ! $template->fields || count($template->fields) === 0 => 'Field belum dipetakan',
                ! $template->is_verified => 'Belum diverifikasi terhadap dokumen sumber',
                ! $template->is_active => 'Belum diaktifkan',
                default => 'Siap digunakan',
            };
        }

        return view('admin.letter-templates.index', compact('templates', 'categories', 'filters'));
    }

    public function create()
    {
        return view('admin.letter-templates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'template_key' => ['required', 'string', 'max:100', 'alpha_dash'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'version' => ['nullable', 'integer', 'min:1', 'max:99'],
            'source_path' => ['nullable', 'string', 'max:255'],
            'numbering_key' => ['nullable', 'string', 'max:100'],
            'numbering_pattern' => ['nullable', 'string', 'max:255'],
            'fields' => ['required', 'string'],
            'template_file' => ['nullable', 'file', 'mimes:docx', 'max:10240'],
            'is_verified' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['fields'] = array_values(array_filter(array_map('trim', preg_split('/\s*,\s*/', $data['fields'] ?? ''))));
        $data['version'] = $data['version'] ?? 1;
        $data['page_width_mm'] = 210.00;
        $data['page_height_mm'] = 297.00;
        $data['orientation'] = 'portrait';
        $data['margins_mm'] = ['top' => 10, 'right' => 20, 'bottom' => 15, 'left' => 20];
        if ($request->hasFile('template_file')) {
            $data['source_path'] = $request->file('template_file')->store('templates/' . str($data['category'])->slug(), 'local');
        }
        unset($data['template_file']);
        $data['is_verified'] = $request->boolean('is_verified');
        $data['is_active'] = $request->boolean('is_active');

        if (($data['is_verified'] || $data['is_active']) && blank($data['source_path'])) {
            throw ValidationException::withMessages([
                'template_file' => 'Template aktif atau terverifikasi wajib memiliki file DOCX sumber.',
            ]);
        }

        $template = LetterTemplate::create($data);

        return redirect()->route('letter-template.index')->with('success', 'Template surat berhasil ditambahkan.');
    }

    public function generate(Request $request, LetterTemplate $template)
    {
        if (! $template->is_active || ! $template->is_verified) {
            throw ValidationException::withMessages([
                'template' => 'Template harus aktif dan sudah diverifikasi sebelum digunakan.',
            ]);
        }

        $fields = $template->fields ?? [];
        $rules = ['body' => ['nullable', 'string']];

        foreach ($fields as $field) {
            $rules[$field] = ['nullable', 'string', 'max:1000'];
        }

        $data = $request->validate($rules);
        $fieldData = [];
        foreach ($fields as $field) {
            $fieldData[$field] = $data[$field] ?? '';
        }

        $resolvedTemplate = $this->templateResolver->resolve($template->source_path);
        if (filled($template->source_path) && $resolvedTemplate === null) {
            throw ValidationException::withMessages([
                'template' => 'File template asli tidak ditemukan. Unggah kembali file DOCX resmi sebelum melakukan generate.',
            ]);
        }

        $issueDate = Carbon::parse($data['tanggal'] ?? $data['issue_date'] ?? now()->toDateString());
        [$letter, $tempFile] = DB::transaction(function () use ($template, $fields, $fieldData, $data, $issueDate, $resolvedTemplate) {
            $sequence = $this->nextSequence((int) $issueDate->year, $template->numbering_key ?? 'MNI');
            $letterNumber = $this->formatLetterNumber($sequence, $template->numbering_pattern ?? '{seq3}/MNI/I/{month_roman}/{year}', $issueDate, (int) $issueDate->year);
            $fieldData = array_merge($fieldData, [
                'nomor_surat' => $letterNumber,
                'letter_number' => $letterNumber,
                'tanggal' => $issueDate->format('d-m-Y'),
                'issue_date' => $issueDate->toDateString(),
            ]);
            $bodyText = $data['body'] ?? $this->defaultBodyText($fields, $fieldData);
            $rendered = $this->renderTemplateText($bodyText, $fieldData);

            $letter = MosqueLetter::create([
                'template_id' => $template->id,
                'year' => (int) $issueDate->year,
                'sequence' => $sequence,
                'letter_code' => $template->numbering_key ?? 'MNI',
                'letter_number' => $letterNumber,
                'issue_date' => $issueDate->toDateString(),
                'letter_type' => $template->name,
                'recipient' => $fieldData['nama_penerima'] ?? $fieldData['recipient'] ?? $data['tujuan_surat'] ?? 'Penerima',
                'subject' => $fieldData['perihal'] ?? $fieldData['subject'] ?? $template->name,
                'body' => $rendered,
                'field_data' => $fieldData,
                'status' => 'Draft',
                'created_by' => auth()->id(),
            ]);

            $generator = new WordDocumentGenerator();
            $tempFile = $resolvedTemplate
                ? $generator->generateFromTemplate($resolvedTemplate[0], $fieldData)
                : $generator->generateFromText($rendered, $template->name . '.docx');

            return [$letter, $tempFile];
        });

        $content = file_get_contents($tempFile);
        $pdfContent = null;
        try {
            $temporaryPdf = (new PdfDocumentGenerator())->generateFromDocx($tempFile);
            $pdfContent = file_get_contents($temporaryPdf);
            unlink($temporaryPdf);
        } catch (\RuntimeException $exception) {
            report($exception);
        }
        unlink($tempFile);
        if ($resolvedTemplate && $resolvedTemplate[1]) {
            @unlink($resolvedTemplate[0]);
        }

        $generatedPath = 'generated/letters/' . $letter->id . '-' . str($template->template_key)->slug() . '.docx';
        $generatedPdfPath = 'generated/letters/' . $letter->id . '-' . str($template->template_key)->slug() . '.pdf';
        Storage::disk('local')->put($generatedPath, $content);
        $letterData = ['docx_path' => $generatedPath];
        if ($pdfContent !== null) {
            Storage::disk('local')->put($generatedPdfPath, $pdfContent);
            $letterData['pdf_path'] = $generatedPdfPath;
        }
        $letter->update($letterData);

        return response($content, 200)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
            ->header('Content-Disposition', 'attachment; filename="' . preg_replace('/[^A-Za-z0-9._-]+/', '_', $template->name) . '-' . $letter->id . '.docx"');
    }

    public function preview(LetterTemplate $template)
    {
        $fields = $template->fields ?? [];
        $sampleValues = [];
        foreach ($fields as $field) {
            $sampleValues[$field] = $this->guessSampleValue($field);
        }

        return view('admin.letter-templates.preview', [
            'template' => $template,
            'fields' => $fields,
            'sampleValues' => $sampleValues,
            'defaultBody' => $this->defaultBodyText($fields, $sampleValues),
        ]);
    }

    public function generateForm(LetterTemplate $template)
    {
        if (! $template->is_active || ! $template->is_verified) {
            throw ValidationException::withMessages([
                'template' => 'Template ini masih draft atau belum diverifikasi.',
            ]);
        }

        $fields = $template->fields ?? [];
        $sampleValues = [];
        foreach ($fields as $field) {
            $sampleValues[$field] = $this->guessSampleValue($field);
        }

        return view('admin.letter-templates.generate', [
            'template' => $template,
            'fields' => $fields,
            'sampleValues' => $sampleValues,
            'defaultBody' => $this->defaultBodyText($fields, $sampleValues),
        ]);
    }

    private function defaultBodyText(array $fields, array $values): string
    {
        $body = [];

        foreach ($fields as $field) {
            if ($field === 'nomor_surat' || $field === 'tanggal' || $field === 'nama_penerima' || $field === 'perihal') {
                continue;
            }
            $body[] = ucfirst(str_replace('_', ' ', $field)) . ': ' . ($values[$field] ?? '');
        }

        if (empty($body)) {
            return "Nomor: {{nomor_surat}}\nTanggal: {{tanggal}}\nKepada: {{nama_penerima}}\nPerihal: {{perihal}}";
        }

        return implode("\n", $body);
    }

    private function renderTemplateText(string $text, array $values): string
    {
        $rendered = $text;
        foreach ($values as $key => $value) {
            $rendered = str_replace('{{' . $key . '}}', (string) $value, $rendered);
            $rendered = str_replace('{' . $key . '}', (string) $value, $rendered);
        }

        return $rendered;
    }

    private function guessSampleValue(string $field): string
    {
        return match ($field) {
            'nomor_surat' => '001',
            'tanggal' => now()->format('d F Y'),
            'nama_penerima' => 'Bapak/Ibu Warga',
            'perihal' => 'Permohonan',
            'jabatan_penerima' => 'Ketua RW',
            'alamat_penerima' => 'Jl. Contoh No. 1',
            'nominal' => 'Rp 1.000.000',
            'terbilang' => 'Satu juta rupiah',
            default => ucfirst(str_replace('_', ' ', $field)),
        };
    }

    private function nextSequence(int $year, string $letterCode): int
    {
        DB::table('letter_sequences')->insertOrIgnore([
            'year' => $year,
            'letter_code' => strtoupper($letterCode),
            'last_number' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $counter = DB::table('letter_sequences')
            ->where('year', $year)
            ->where('letter_code', strtoupper($letterCode))
            ->lockForUpdate()
            ->first();

        $next = (int) ($counter->last_number ?? 0) + 1;

        DB::table('letter_sequences')
            ->where('year', $year)
            ->where('letter_code', strtoupper($letterCode))
            ->update(['last_number' => $next, 'updated_at' => now()]);

        return $next;
    }

    private function formatLetterNumber(int $sequence, string $pattern, Carbon $date, int $year): string
    {
        $monthRoman = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'][$date->month];

        $replacements = [
            '{seq}' => (string) $sequence,
            '{seq3}' => str_pad((string) $sequence, 3, '0', STR_PAD_LEFT),
            '{month_roman}' => $monthRoman,
            '{year}' => (string) $year,
            '{year_short}' => substr((string) $year, -2),
            '{month}' => (string) $date->month,
        ];

        return strtr($pattern, $replacements);
    }
}
