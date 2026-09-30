<?php

namespace App\Http\Controllers;

use App\Models\MosqueLetter;
use App\Models\OrphanDonation;
use App\Models\OrphanDistribution;
use App\Models\OrphanRecipient;
use App\Models\QurbanCommitteeMember;
use App\Models\QurbanContribution;
use App\Models\QurbanTransaction;
use App\Models\RamadanSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class MosqueOperationsController extends Controller
{
    private const MODULES = [
        'ramadan' => 'Ramadan',
        'qurban-team' => 'Panitia Qurban',
        'qurban-contributions' => 'Peserta Qurban',
        'qurban-finance' => 'Kas Qurban',
        'charity-beneficiaries' => 'Penerima Santunan',
        'charity-donations' => 'Donasi Santunan',
        'charity-distributions' => 'Penyaluran Santunan',
        'letters' => 'Persuratan',
    ];

    public function index(Request $request)
    {
        $validated = $request->validate([
            'module' => ['nullable', 'string', 'in:' . implode(',', array_keys(self::MODULES))],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
            'edit' => ['nullable', 'integer', 'min:1'],
            'q' => ['nullable', 'string', 'max:100'],
            'archived' => ['nullable', 'boolean'],
        ]);

        $module = $validated['module'] ?? 'ramadan';
        $moduleLabel = self::MODULES[$module];
        $year = (int) ($validated['year'] ?? now()->year);
        $search = trim($validated['q'] ?? '');
        $showArchived = (bool) ($validated['archived'] ?? false);
        $records = [
            'ramadan' => RamadanSchedule::where('year', $year)->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('kind', 'like', "%{$search}%")->orWhere('person_name', 'like', "%{$search}%")->orWhere('title', 'like', "%{$search}%");
            }))->orderBy('date')->get(),
            'qurban-team' => QurbanCommitteeMember::where('year', $year)->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")->orWhere('position', 'like', "%{$search}%");
            }))->orderBy('position')->orderBy('name')->get(),
            'qurban-contributions' => QurbanContribution::where('year', $year)->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('animal_type', 'like', "%{$search}%")->orWhere('animal_group', 'like', "%{$search}%")->orWhere('participant_name', 'like', "%{$search}%");
            }))->orderBy('animal_type')->orderBy('animal_group')->orderBy('participant_name')->get(),
            'qurban-finance' => QurbanTransaction::where('year', $year)->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('category', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%")->orWhere('party_name', 'like', "%{$search}%");
            }))->orderByDesc('date')->get(),
            'charity-beneficiaries' => OrphanRecipient::where('year', $year)->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")->orWhere('group_name', 'like', "%{$search}%");
            }))->orderBy('name')->get(),
            'charity-donations' => OrphanDonation::where('year', $year)->when($search, fn ($query) => $query->where('donor_name', 'like', "%{$search}%"))->orderByDesc('date')->get(),
            'charity-distributions' => OrphanDistribution::with('recipient')->where('year', $year)->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->whereHas('recipient', fn ($recipient) => $recipient->where('name', 'like', "%{$search}%"))->orWhere('description', 'like', "%{$search}%");
            }))->orderByDesc('date')->get(),
            'letters' => MosqueLetter::where('year', $year)->when(! $showArchived, fn ($query) => $query->whereNull('archived_at'))->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('letter_number', 'like', "%{$search}%")->orWhere('letter_type', 'like', "%{$search}%")->orWhere('recipient', 'like', "%{$search}%")->orWhere('subject', 'like', "%{$search}%");
            }))->orderByDesc('sequence')->get(),
        ];

        $editing = null;
        if (!empty($validated['edit'])) {
            $editing = $this->queryFor($module, $year)->findOrFail($validated['edit']);
        }
        $isEditing = $editing !== null;
        $formAction = $isEditing
            ? route('operations.update', ['module' => $module, 'id' => $editing->id])
            : route('operations.store', ['module' => $module]);

        $qurbanIncome = QurbanTransaction::where('year', $year)->where('transaction_type', 'Pemasukan')->sum('amount');
        $qurbanExpense = QurbanTransaction::where('year', $year)->where('transaction_type', 'Pengeluaran')->sum('amount');
        $charityDonations = OrphanDonation::where('year', $year)->sum('amount');
        $charityDistributions = OrphanDistribution::where('year', $year)->sum('amount');
        $years = range(max(2000, now()->year - 5), now()->year + 1);

        return view('admin.operations', compact(
            'module', 'year', 'search', 'showArchived', 'years', 'records', 'editing', 'qurbanIncome', 'qurbanExpense',
            'charityDonations', 'charityDistributions', 'isEditing', 'formAction'
        ))->with('modules', self::MODULES)->with('moduleLabel', $moduleLabel);
    }

    public function store(Request $request, string $module)
    {
        abort_unless(isset(self::MODULES[$module]), 404);
        $data = $this->validatedData($request, $module);

        if ($module === 'letters') {
            $this->createLetter($data);
        } else {
            $this->recordClass($module)::create($data);
        }

        return $this->redirectToModule($module, $data['year'], 'Data berhasil ditambahkan.');
    }

    public function update(Request $request, string $module, int $id)
    {
        abort_unless(isset(self::MODULES[$module]), 404);
        $data = $this->validatedData($request, $module);
        $class = $this->recordClass($module);
        $record = $class::findOrFail($id);

        if ($module === 'letters') {
            if ((int) $data['year'] !== (int) $record->year) {
                return redirect()->route('operations.index', ['module' => $module, 'year' => $record->year, 'edit' => $record->id])
                    ->withErrors(['issue_date' => 'Tahun surat yang sudah bernomor tidak dapat diubah. Buat surat baru untuk tahun berbeda.']);
            }

            DB::transaction(function () use ($data, $record) {
                $sequence = $data['letter_code'] === $record->letter_code
                    ? $record->sequence
                    : $this->nextLetterSequence($record->year, $data['letter_code']);
                $data['sequence'] = $sequence;
                $data['letter_number'] = $this->formatLetterNumber($sequence, $data['letter_code'], $data['issue_date'], $record->year);
                $record->update($data);
            });
        } else {
            $record->update($data);
        }

        return $this->redirectToModule($module, $data['year'], 'Data berhasil diperbarui.');
    }

    public function destroy(Request $request, string $module, int $id)
    {
        abort_unless(isset(self::MODULES[$module]), 404);
        $year = (int) $request->validate(['year' => ['required', 'integer', 'between:2000,2100']])['year'];
        $record = $this->queryFor($module, $year)->findOrFail($id);

        if ($module === 'charity-beneficiaries' && $record->distributions()->exists()) {
            return $this->redirectToModule($module, $year, 'Penerima tidak dapat dihapus karena sudah memiliki catatan penyaluran.');
        }

        if ($module === 'letters') {
            $record->update(['archived_at' => now()]);
        } else {
            $record->delete();
        }

        return $this->redirectToModule($module, $year, 'Data berhasil dihapus.');
    }

    public function printLetter(MosqueLetter $letter)
    {
        $letter->load('template');
        $settings = \App\Models\MosqueSetting::pluck('setting_value', 'setting_key');

        if (filled($letter->pdf_path) && Storage::disk('local')->exists($letter->pdf_path)) {
            return response()->file(Storage::disk('local')->path($letter->pdf_path), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($letter->pdf_path) . '"',
            ]);
        }

        if ($letter->template?->template_key === 'ZAKAT_EDARAN_2025') {
            return view('admin.letters.zakat-circular-print', compact('letter', 'settings'));
        }

        return view('admin.mosque-letter-print', compact('letter'));
    }

    public function downloadDocx(MosqueLetter $letter)
    {
        abort_if(blank($letter->docx_path) || ! Storage::disk('local')->exists($letter->docx_path), 404);

        return Storage::disk('local')->download($letter->docx_path, basename($letter->docx_path), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }

    public function downloadPdf(MosqueLetter $letter)
    {
        abort_if(blank($letter->pdf_path) || ! Storage::disk('local')->exists($letter->pdf_path), 404);

        return Storage::disk('local')->download($letter->pdf_path, basename($letter->pdf_path), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function printZakatHandover(\App\Models\ZakatReceipt $receipt, Request $request)
    {
        $settings = \App\Models\MosqueSetting::pluck('setting_value', 'setting_key');
        $official = $receipt->officer_name;
        $showStamp = $request->boolean('stamp');

        return view('admin.zakat-handover-print', compact('receipt', 'settings', 'official', 'showStamp'));
    }

    public function referenceAsset(string $asset)
    {
        $assets = [
            'kop-fix' => ['MASJID/KOP FIX.png', null],
            'kop' => ['MASJID/KOP.png', null],
            'cap-clean' => ['MASJID/cap_msjid_fix-removebg-preview.png', null],
            'cap-scan' => ['MASJID/cap masjid.jpeg', null],
            'surat-edaran-mark' => ['MASJID/ROMADON/Zakat/EDARAN ZAKAT DAN TANDA TERIMA/SURAT EDARAN ZAKAT FITRAH.docx', 'word/media/image1.png'],
            'tanda-zakat-mark' => ['MASJID/ROMADON/Zakat/EDARAN ZAKAT DAN TANDA TERIMA/TANDA PENYERAHAN ZAKAT FITRAH.docx', 'word/media/image1.png'],
        ];

        abort_unless(isset($assets[$asset]), 404);
        [$sourcePath, $embeddedPath] = $assets[$asset];
        $archive = new ZipArchive();
        abort_unless($archive->open(storage_path('app/referensi-masjid/MASJID.zip')) === true, 404);
        $contents = $archive->getFromName($sourcePath);

        if ($embeddedPath !== null && $contents !== false) {
            $temporaryFile = tempnam(sys_get_temp_dir(), 'masjid-template-');
            file_put_contents($temporaryFile, $contents);
            $document = new ZipArchive();
            $documentOpened = $document->open($temporaryFile) === true;
            $contents = $documentOpened ? $document->getFromName($embeddedPath) : false;
            if ($documentOpened) $document->close();
            unlink($temporaryFile);
        }

        $archive->close();
        abort_if($contents === false, 404);
        $mime = str_ends_with(strtolower($sourcePath), '.jpeg') ? 'image/jpeg' : 'image/png';

        return response($contents)->header('Content-Type', $mime)->header('Cache-Control', 'private, max-age=3600');
    }

    private function validatedData(Request $request, string $module): array
    {
        $rules = match ($module) {
            'ramadan' => [
                'kind' => ['required', 'in:Kultum,Takjil'],
                'date' => ['required', 'date'],
                'title' => ['nullable', 'string', 'max:255'],
                'person_name' => ['nullable', 'string', 'max:255'],
                'quantity' => ['nullable', 'integer', 'min:1', 'max:10000'],
                'notes' => ['nullable', 'string', 'max:2000'],
                'is_public' => ['nullable', 'boolean'],
            ],
            'qurban-team' => [
                'year' => ['required', 'integer', 'between:2000,2100'],
                'name' => ['required', 'string', 'max:255'],
                'position' => ['required', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:30'],
                'notes' => ['nullable', 'string', 'max:2000'],
            ],
            'qurban-contributions' => [
                'year' => ['required', 'integer', 'between:2000,2100'],
                'animal_type' => ['required', 'in:Sapi,Kambing'],
                'animal_group' => ['nullable', 'string', 'max:100'],
                'participant_name' => ['required', 'string', 'max:255'],
                'amount' => ['required', 'numeric', 'min:0', 'max:9999999999999'],
                'paid_amount' => ['required', 'numeric', 'min:0', 'lte:amount'],
                'paid_at' => ['nullable', 'date'],
                'notes' => ['nullable', 'string', 'max:2000'],
            ],
            'qurban-finance' => [
                'year' => ['required', 'integer', 'between:2000,2100'],
                'transaction_type' => ['required', 'in:Pemasukan,Pengeluaran'],
                'date' => ['required', 'date'],
                'category' => ['required', 'string', 'max:100'],
                'description' => ['required', 'string', 'max:255'],
                'party_name' => ['nullable', 'string', 'max:255'],
                'amount' => ['required', 'numeric', 'min:1', 'max:9999999999999'],
                'notes' => ['nullable', 'string', 'max:2000'],
            ],
            'charity-beneficiaries' => [
                'year' => ['required', 'integer', 'between:2000,2100'],
                'name' => ['required', 'string', 'max:255'],
                'guardian_name' => ['nullable', 'string', 'max:255'],
                'group_name' => ['nullable', 'string', 'max:100'],
                'private_notes' => ['nullable', 'string', 'max:2000'],
                'is_active' => ['nullable', 'boolean'],
            ],
            'charity-donations' => [
                'year' => ['required', 'integer', 'between:2000,2100'],
                'date' => ['required', 'date'],
                'donor_name' => ['required', 'string', 'max:255'],
                'amount' => ['required', 'numeric', 'min:1', 'max:9999999999999'],
                'payment_method' => ['nullable', 'string', 'max:50'],
                'private_notes' => ['nullable', 'string', 'max:2000'],
            ],
            'charity-distributions' => [
                'year' => ['required', 'integer', 'between:2000,2100'],
                'recipient_id' => ['required', 'exists:orphan_recipients,id,year,' . (int) $request->input('year')],
                'date' => ['required', 'date'],
                'description' => ['required', 'string', 'max:255'],
                'amount' => ['required', 'numeric', 'min:0', 'max:9999999999999'],
                'private_notes' => ['nullable', 'string', 'max:2000'],
            ],
            'letters' => [
                'issue_date' => ['required', 'date'],
                'letter_type' => ['required', 'string', 'max:100'],
                'letter_code' => ['required', 'string', 'alpha_dash', 'max:50'],
                'recipient' => ['required', 'string', 'max:255'],
                'subject' => ['required', 'string', 'max:255'],
                'body' => ['required', 'string', 'max:30000'],
                'status' => ['required', 'in:Draft,Terbit'],
            ],
        };

        $data = $request->validate($rules);
        if ($module === 'ramadan') {
            $data['year'] = Carbon::parse($data['date'])->year;
            $data['is_public'] = $request->boolean('is_public');
        }
        if ($module === 'charity-beneficiaries') {
            $data['is_active'] = $request->boolean('is_active', true);
        }
        if ($module === 'letters') {
            $data['year'] = Carbon::parse($data['issue_date'])->year;
        }

        return $data;
    }

    private function createLetter(array $data): MosqueLetter
    {
        return DB::transaction(function () use ($data) {
            $year = (int) $data['year'];
            $sequence = $this->nextLetterSequence($year, $data['letter_code']);

            return MosqueLetter::create(array_merge($data, [
                'sequence' => $sequence,
                'letter_number' => $this->formatLetterNumber($sequence, $data['letter_code'], $data['issue_date'], $year),
            ]));
        });
    }

    private function nextLetterSequence(int $year, string $letterCode): int
    {
        $now = now();
        DB::table('letter_sequences')->insertOrIgnore([
            'year' => $year,
            'letter_code' => strtoupper($letterCode),
            'last_number' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $counter = DB::table('letter_sequences')
            ->where('year', $year)
            ->where('letter_code', strtoupper($letterCode))
            ->lockForUpdate()
            ->first();
        $nextNumber = (int) $counter->last_number + 1;

        DB::table('letter_sequences')
            ->where('year', $year)
            ->where('letter_code', strtoupper($letterCode))
            ->update(['last_number' => $nextNumber, 'updated_at' => $now]);

        return $nextNumber;
    }

    private function formatLetterNumber(int $sequence, string $letterCode, string $issueDate, int $year): string
    {
        $romanMonth = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'][Carbon::parse($issueDate)->month];

        return sprintf('%03d/%s/%s/%d', $sequence, strtoupper($letterCode), $romanMonth, $year);
    }

    private function recordClass(string $module): string
    {
        return match ($module) {
            'ramadan' => RamadanSchedule::class,
            'qurban-team' => QurbanCommitteeMember::class,
            'qurban-contributions' => QurbanContribution::class,
            'qurban-finance' => QurbanTransaction::class,
            'charity-beneficiaries' => OrphanRecipient::class,
            'charity-donations' => OrphanDonation::class,
            'charity-distributions' => OrphanDistribution::class,
            'letters' => MosqueLetter::class,
        };
    }

    private function queryFor(string $module, int $year)
    {
        $class = $this->recordClass($module);
        return $class::where('year', $year);
    }

    private function redirectToModule(string $module, int $year, string $message)
    {
        return redirect()->route('operations.index', ['module' => $module, 'year' => $year])->with('success', $message);
    }
}