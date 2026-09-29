<?php

namespace App\Http\Controllers;

use App\Models\MosqueSetting;
use App\Models\Official;
use App\Models\LetterTemplate;
use App\Models\ZakatDistribution;
use App\Models\ZakatPeriod;
use App\Models\ZakatReceipt;
use App\Models\ZakatReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ZakatAdminController extends Controller
{
    private const TABS = ['ringkasan', 'periode', 'penerimaan', 'distribusi', 'laporan', 'pengaturan'];

    public function index(Request $request)
    {
        $validated = $request->validate([
            'year' => ['nullable', 'integer', 'between:2000,2100'],
            'tab' => ['nullable', 'in:' . implode(',', self::TABS)],
            'edit_receipt' => ['nullable', 'integer', 'min:1'],
            'edit_distribution' => ['nullable', 'integer', 'min:1'],
        ]);

        $year = (int) ($validated['year'] ?? now()->year);
        $tab = $validated['tab'] ?? 'ringkasan';
        $period = ZakatPeriod::where('year', $year)->first();
        $receipts = $period ? $period->receipts()->orderByDesc('date')->get() : collect();
        $distributions = $period ? $period->distributions()->orderByDesc('date')->get() : collect();
        $receiptEditing = isset($validated['edit_receipt'])
            ? $receipts->firstWhere('id', (int) $validated['edit_receipt'])
            : null;
        $distributionEditing = isset($validated['edit_distribution'])
            ? $distributions->firstWhere('id', (int) $validated['edit_distribution'])
            : null;

        abort_if(isset($validated['edit_receipt']) && !$receiptEditing, 404);
        abort_if(isset($validated['edit_distribution']) && !$distributionEditing, 404);

        return view('admin.zakat', [
            'year' => $year,
            'years' => range(max(2000, now()->year - 5), now()->year + 1),
            'tab' => $tab,
            'tabs' => self::TABS,
            'period' => $period,
            'receipts' => $receipts,
            'distributions' => $distributions,
            'receiptEditing' => $receiptEditing,
            'distributionEditing' => $distributionEditing,
            'summary' => $this->summary($period),
            'reports' => $period ? $period->reports()->latest()->get() : collect(),
            'settings' => MosqueSetting::pluck('setting_value', 'setting_key'),
            'officials' => Official::orderBy('sort_order')->orderBy('name')->get(),
            'letterTemplates' => LetterTemplate::orderBy('category')->orderBy('name')->orderByDesc('version')->get(),
        ]);
    }

    public function storePeriod(Request $request)
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'between:2000,2100'],
            'hijri_year' => ['nullable', 'string', 'max:12'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'fitrah_kg_per_person' => ['required', 'numeric', 'gt:0', 'max:100'],
            'zakat_mal_rate_percent' => ['nullable', 'numeric', 'gte:0', 'lte:100'],
            'fidyah_kg_per_day' => ['nullable', 'numeric', 'gte:0', 'max:100'],
            'fidyah_money_per_day' => ['nullable', 'numeric', 'gte:0', 'max:999999999999'],
            'service_hours' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:Draft,Aktif,Ditutup'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        ZakatPeriod::updateOrCreate(['year' => $data['year']], $data);

        return $this->redirect($data['year'], 'periode', 'Periode zakat berhasil disimpan.');
    }

    public function storeCircular(Request $request, ZakatPeriod $period)
    {
        abort_if($period->status === 'Ditutup', 403, 'Periode zakat sudah ditutup.');
        $data = $request->validate([
            'issue_date' => ['required', 'date'],
            'recipient' => ['required', 'string', 'max:255'],
            'service_hours' => ['required', 'string', 'max:255'],
            'venue' => ['required', 'string', 'max:255'],
            'closing_hijri_day' => ['required', 'string', 'max:50'],
            'chairperson_id' => ['required', 'exists:officials,id'],
            'secretary_id' => ['nullable', 'exists:officials,id'],
            'knowing_official_id' => ['required', 'exists:officials,id'],
            'cc' => ['nullable', 'string', 'max:1000'],
            'show_stamp' => ['nullable', 'boolean'],
        ]);

        $template = LetterTemplate::where('template_key', 'ZAKAT_EDARAN_2025')->firstOrFail();
        if (!$template->is_active || !$template->is_verified || !$template->numbering_pattern) {
            return $this->redirect($period->year, 'periode', 'Template Surat Edaran belum diverifikasi, diaktifkan, dan diberi pola nomor pada tab Data Masjid.');
        }
        if (!$period->hijri_year || !$period->ends_at || $period->zakat_mal_rate_percent === null || $period->fidyah_kg_per_day === null || $period->fidyah_money_per_day === null) {
            return $this->redirect($period->year, 'periode', 'Lengkapi tahun Hijriah, akhir pelayanan, persentase zakat mal, dan ketentuan fidyah pada pengaturan periode.');
        }

        if (Carbon::parse($data['issue_date'])->year !== (int) $period->year) {
            return back()->withInput()->withErrors(['issue_date' => 'Tanggal surat harus berada pada tahun periode zakat yang dipilih.']);
        }

        $officialIds = array_filter([$data['chairperson_id'], $data['secretary_id'] ?? null, $data['knowing_official_id']]);
        $officials = Official::whereIn('id', $officialIds)->where('is_active', true)->get()->keyBy('id');
        $chairperson = $officials->get((int) $data['chairperson_id']);
        $secretary = isset($data['secretary_id']) ? $officials->get((int) $data['secretary_id']) : null;
        $knowing = $officials->get((int) $data['knowing_official_id']);
        if (!$chairperson || !$chairperson->is_signatory || ($secretary && !$secretary->is_signatory) || !$knowing || !$knowing->is_signatory) {
            return back()->withInput()->withErrors(['chairperson_id' => 'Pilih ketua, sekretaris, dan pihak mengetahui yang aktif sebagai penandatangan.']);
        }

        $letter = DB::transaction(function () use ($data, $period, $template, $chairperson, $secretary, $knowing, $request) {
            $year = (int) $period->year;
            $code = (string) $template->numbering_key;
            $now = now();
            DB::table('letter_sequences')->insertOrIgnore([
                'year' => $year,
                'letter_code' => $code,
                'last_number' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $counter = DB::table('letter_sequences')->where('year', $year)->where('letter_code', $code)->lockForUpdate()->first();
            $sequence = (int) $counter->last_number + 1;
            DB::table('letter_sequences')->where('year', $year)->where('letter_code', $code)->update(['last_number' => $sequence, 'updated_at' => $now]);

            $issueDate = Carbon::parse($data['issue_date']);
            $romanMonth = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'][$issueDate->month];
            $number = strtr($template->numbering_pattern, [
                '{seq}' => (string) $sequence,
                '{seq3}' => str_pad((string) $sequence, 3, '0', STR_PAD_LEFT),
                '{month_roman}' => $romanMonth,
                '{year}' => (string) $year,
                '{year_short}' => substr((string) $year, -2),
                '{hijri_year}' => (string) ($period->hijri_year ?? ''),
            ]);

            return \App\Models\MosqueLetter::create([
                'template_id' => $template->id,
                'year' => $year,
                'sequence' => $sequence,
                'letter_code' => $code,
                'letter_number' => $number,
                'issue_date' => $issueDate->toDateString(),
                'letter_type' => $template->name,
                'recipient' => $data['recipient'],
                'subject' => 'Edaran Zakat Fitrah, Zakat Mal, Fidyah, Infaq, dan Shodaqoh',
                'event_name' => 'Ramadhan ' . $period->hijri_year,
                'signed_by' => $chairperson->name . ($secretary ? ' / ' . $secretary->name : '') . ' / ' . $knowing->name,
                'body' => '',
                'field_data' => [
                    'recipient' => $data['recipient'],
                    'service_hours' => $data['service_hours'],
                    'venue' => $data['venue'],
                    'closing_hijri_day' => $data['closing_hijri_day'] ?? '',
                    'chairperson_id' => $chairperson->id,
                    'chairperson_name' => trim($chairperson->name . ' ' . $chairperson->title),
                    'secretary_id' => $secretary?->id,
                    'secretary_name' => $secretary ? trim($secretary->name . ' ' . $secretary->title) : null,
                    'knowing_official_id' => $knowing->id,
                    'knowing_official_name' => trim($knowing->name . ' ' . $knowing->title),
                    'cc' => $data['cc'] ?? "Ketua RW IX Kel Krapyak\nArsip",
                    'show_stamp' => $request->boolean('show_stamp'),
                    'hijri_year' => $period->hijri_year,
                    'period_start' => $period->starts_at ? Carbon::parse($period->starts_at)->toDateString() : null,
                    'period_end' => $period->ends_at ? Carbon::parse($period->ends_at)->toDateString() : null,
                    'fitrah_kg_per_person' => $period->fitrah_kg_per_person,
                    'zakat_mal_rate_percent' => $period->zakat_mal_rate_percent,
                    'fidyah_kg_per_day' => $period->fidyah_kg_per_day,
                    'fidyah_money_per_day' => $period->fidyah_money_per_day,
                ],
                'status' => 'Draft',
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->route('zakat-admin.circular.print', $letter);
    }

    public function storeReceipt(Request $request)
    {
        $data = $this->validatedReceipt($request);
        $period = $this->openPeriod($data['year']);
        unset($data['year']);
        $period->receipts()->create($data);

        return $this->redirect($period->year, 'penerimaan', 'Penerimaan zakat berhasil dicatat.');
    }

    public function updateReceipt(Request $request, int $id)
    {
        $data = $this->validatedReceipt($request);
        $period = $this->openPeriod($data['year']);
        $receipt = $period->receipts()->findOrFail($id);
        unset($data['year']);
        $receipt->update($data);

        return $this->redirect($period->year, 'penerimaan', 'Catatan penerimaan berhasil diperbarui.');
    }

    public function destroyReceipt(Request $request, int $id)
    {
        $year = (int) $request->validate(['year' => ['required', 'integer', 'between:2000,2100']])['year'];
        $period = $this->openPeriod($year);
        $period->receipts()->findOrFail($id)->delete();

        return $this->redirect($year, 'penerimaan', 'Catatan penerimaan berhasil dihapus.');
    }

    public function storeDistribution(Request $request)
    {
        $data = $this->validatedDistribution($request);
        $period = $this->openPeriod($data['year']);
        unset($data['year']);
        $period->distributions()->create($data);

        return $this->redirect($period->year, 'distribusi', 'Distribusi zakat berhasil dicatat.');
    }

    public function updateDistribution(Request $request, int $id)
    {
        $data = $this->validatedDistribution($request);
        $period = $this->openPeriod($data['year']);
        $distribution = $period->distributions()->findOrFail($id);
        unset($data['year']);
        $distribution->update($data);

        return $this->redirect($period->year, 'distribusi', 'Catatan distribusi berhasil diperbarui.');
    }

    public function destroyDistribution(Request $request, int $id)
    {
        $year = (int) $request->validate(['year' => ['required', 'integer', 'between:2000,2100']])['year'];
        $period = $this->openPeriod($year);
        $period->distributions()->findOrFail($id)->delete();

        return $this->redirect($year, 'distribusi', 'Catatan distribusi berhasil dihapus.');
    }

    public function printReport(ZakatPeriod $period)
    {
        $summary = $this->summary($period);
        $snapshot = [
            'period' => $period->only(['year', 'hijri_year', 'starts_at', 'ends_at']),
            'summary' => $summary,
            'receipts' => $period->receipts()->orderBy('date')->get()->toArray(),
            'distributions' => $period->distributions()->where('status', 'Sudah Disalurkan')->orderBy('date')->get()->toArray(),
        ];
        $report = ZakatReport::create([
            'period_id' => $period->id,
            'report_type' => 'hasil-akhir-zakat',
            'summary' => $snapshot,
            'generated_by' => auth()->id(),
        ]);

        return view('admin.zakat-report-print', [
            'period' => $period,
            'periodSnapshot' => $snapshot['period'],
            'summary' => $summary,
            'report' => $report,
        ]);
    }

    public function printArchivedReport(ZakatReport $report)
    {
        $snapshot = $report->summary;
        return view('admin.zakat-report-print', [
            'period' => $report->period,
            'periodSnapshot' => $snapshot['period'],
            'summary' => $snapshot['summary'],
            'report' => $report,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'mosque_name' => ['required', 'string', 'max:255'],
            'mosque_address' => ['required', 'string', 'max:500'],
            'secretariat_address' => ['required', 'string', 'max:500'],
            'mosque_phone' => ['nullable', 'string', 'max:50'],
            'letterhead_asset' => ['required', 'in:MASJID/KOP FIX.png,MASJID/KOP.png'],
            'stamp_asset' => ['required', 'in:MASJID/cap_msjid_fix-removebg-preview.png,MASJID/cap masjid.jpeg'],
        ]);

        foreach ($data as $key => $value) {
            MosqueSetting::updateOrCreate(['setting_key' => $key], ['setting_value' => $value]);
        }

        return $this->redirect(now()->year, 'pengaturan', 'Identitas masjid berhasil diperbarui.');
    }

    public function storeOfficial(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:100'],
            'position' => ['required', 'string', 'max:150'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_signatory' => ['nullable', 'boolean'],
        ]);
        $data['is_signatory'] = $request->boolean('is_signatory');
        $data['is_active'] = true;
        Official::create($data);

        return $this->redirect(now()->year, 'pengaturan', 'Data pengurus berhasil ditambahkan.');
    }

    public function updateTemplate(Request $request, LetterTemplate $template)
    {
        $data = $request->validate([
            'page_width_mm' => ['required', 'numeric', 'min:100', 'max:1000'],
            'page_height_mm' => ['required', 'numeric', 'min:100', 'max:1000'],
            'orientation' => ['required', 'in:portrait,landscape'],
            'margin_top_mm' => ['required', 'numeric', 'min:0', 'max:100'],
            'margin_right_mm' => ['required', 'numeric', 'min:0', 'max:100'],
            'margin_bottom_mm' => ['required', 'numeric', 'min:0', 'max:100'],
            'margin_left_mm' => ['required', 'numeric', 'min:0', 'max:100'],
            'fields_json' => ['required', 'json'],
            'numbering_key' => ['nullable', 'string', 'alpha_dash', 'max:100'],
            'numbering_pattern' => ['nullable', 'string', 'max:255'],
            'is_verified' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['margins_mm'] = [
            'top' => (float) $data['margin_top_mm'],
            'right' => (float) $data['margin_right_mm'],
            'bottom' => (float) $data['margin_bottom_mm'],
            'left' => (float) $data['margin_left_mm'],
        ];
        $data['fields'] = json_decode($data['fields_json'], true);
        unset($data['margin_top_mm'], $data['margin_right_mm'], $data['margin_bottom_mm'], $data['margin_left_mm'], $data['fields_json']);
        $data['is_verified'] = $request->boolean('is_verified');
        $data['is_active'] = $request->boolean('is_active') && $data['is_verified'];
        $template->update($data);

        return $this->redirect(now()->year, 'pengaturan', 'Versi template diperbarui. Template hanya aktif setelah ditandai terverifikasi.');
    }

    private function validatedReceipt(Request $request): array
    {
        return $request->validate([
            'year' => ['required', 'integer', 'between:2000,2100'],
            'date' => ['required', 'date'],
            'muzakki_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'souls' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'fitrah_rice_kg' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'fitrah_money' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'zakat_mal' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'fidyah_days' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'fidyah_rice_kg' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'fidyah_money' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'infaq' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'shodaqoh' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'officer_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function validatedDistribution(Request $request): array
    {
        return $request->validate([
            'year' => ['required', 'integer', 'between:2000,2100'],
            'date' => ['required', 'date'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'asnaf' => ['nullable', 'string', 'max:100'],
            'fitrah_rice_kg' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'fitrah_money' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'zakat_mal' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'fidyah_rice_kg' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'fidyah_money' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'infaq' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'shodaqoh' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'status' => ['required', 'in:Belum Disalurkan,Sudah Disalurkan'],
            'distributed_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function openPeriod(int $year): ZakatPeriod
    {
        $period = ZakatPeriod::where('year', $year)->firstOrFail();
        abort_if($period->status === 'Ditutup', 403, 'Periode zakat sudah ditutup.');
        return $period;
    }

    private function summary(?ZakatPeriod $period): array
    {
        $zero = [
            'receipts' => ['souls' => 0, 'fitrah_rice_kg' => 0, 'fitrah_money' => 0, 'zakat_mal' => 0, 'fidyah_rice_kg' => 0, 'fidyah_money' => 0, 'infaq' => 0, 'shodaqoh' => 0],
            'distributions' => ['recipients' => 0, 'fitrah_rice_kg' => 0, 'fitrah_money' => 0, 'zakat_mal' => 0, 'fidyah_rice_kg' => 0, 'fidyah_money' => 0, 'infaq' => 0, 'shodaqoh' => 0],
            'remaining' => ['fitrah_rice_kg' => 0, 'fitrah_money' => 0, 'zakat_mal' => 0, 'fidyah_rice_kg' => 0, 'fidyah_money' => 0],
        ];
        if (!$period) return $zero;

        $receipts = $period->receipts();
        $distributed = $period->distributions()->where('status', 'Sudah Disalurkan');
        $receiptTotals = [
            'souls' => (int) (clone $receipts)->sum('souls'),
            'fitrah_rice_kg' => (float) (clone $receipts)->sum('fitrah_rice_kg'),
            'fitrah_money' => (float) (clone $receipts)->sum('fitrah_money'),
            'zakat_mal' => (float) (clone $receipts)->sum('zakat_mal'),
            'fidyah_rice_kg' => (float) (clone $receipts)->sum('fidyah_rice_kg'),
            'fidyah_money' => (float) (clone $receipts)->sum('fidyah_money'),
            'infaq' => (float) (clone $receipts)->sum('infaq'),
            'shodaqoh' => (float) (clone $receipts)->sum('shodaqoh'),
        ];
        $distributionTotals = [
            'recipients' => (clone $distributed)->distinct('recipient_name')->count('recipient_name'),
            'fitrah_rice_kg' => (float) (clone $distributed)->sum('fitrah_rice_kg'),
            'fitrah_money' => (float) (clone $distributed)->sum('fitrah_money'),
            'zakat_mal' => (float) (clone $distributed)->sum('zakat_mal'),
            'fidyah_rice_kg' => (float) (clone $distributed)->sum('fidyah_rice_kg'),
            'fidyah_money' => (float) (clone $distributed)->sum('fidyah_money'),
            'infaq' => (float) (clone $distributed)->sum('infaq'),
            'shodaqoh' => (float) (clone $distributed)->sum('shodaqoh'),
        ];

        return [
            'receipts' => $receiptTotals,
            'distributions' => $distributionTotals,
            'remaining' => [
                'fitrah_rice_kg' => $receiptTotals['fitrah_rice_kg'] - $distributionTotals['fitrah_rice_kg'],
                'fitrah_money' => $receiptTotals['fitrah_money'] - $distributionTotals['fitrah_money'],
                'zakat_mal' => $receiptTotals['zakat_mal'] - $distributionTotals['zakat_mal'],
                'fidyah_rice_kg' => $receiptTotals['fidyah_rice_kg'] - $distributionTotals['fidyah_rice_kg'],
                'fidyah_money' => $receiptTotals['fidyah_money'] - $distributionTotals['fidyah_money'],
            ],
        ];
    }

    private function redirect(int $year, string $tab, string $message)
    {
        return redirect()->route('zakat-admin.index', ['year' => $year, 'tab' => $tab])->with('success', $message);
    }
}