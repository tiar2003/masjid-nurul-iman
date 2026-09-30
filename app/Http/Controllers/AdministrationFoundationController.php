<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdministrationFoundationController extends Controller
{
    private const SECTIONS = [
        'management' => 'Pengurus',
        'documents' => 'Surat Masuk / Keluar',
        'proposals' => 'Proposal',
        'qurban-animals' => 'Hewan Qurban',
        'qurban-distributions' => 'Distribusi Qurban',
        'ramadan-donors' => 'Donatur Ramadan',
        'charity-collections' => 'Kotak Amal',
    ];

    public function index(Request $request)
    {
        $section = $request->validate(['section' => ['nullable', 'in:' . implode(',', array_keys(self::SECTIONS))]])['section'] ?? 'management';
        $data = match ($section) {
            'management' => [
                'periods' => DB::table('management_periods')->latest()->get(),
                'positions' => DB::table('positions')->where('is_active', true)->orderBy('sort_order')->get(),
                'members' => DB::table('management_members')->leftJoin('positions', 'positions.id', '=', 'management_members.position_id')->select('management_members.*', 'positions.name as position_name')->orderBy('management_members.sort_order')->paginate(20, ['*'], 'members_page'),
            ],
            'documents' => ['records' => DB::table('secretariat_documents')->latest('document_date')->paginate(20)],
            'proposals' => ['records' => DB::table('proposals')->latest('proposal_date')->paginate(20)],
            'qurban-animals' => ['records' => DB::table('qurban_animals')->latest()->paginate(20)],
            'qurban-distributions' => ['records' => DB::table('qurban_distributions')->latest('distribution_date')->paginate(20)],
            'ramadan-donors' => ['records' => DB::table('ramadan_donors')->latest('donation_date')->paginate(20)],
            'charity-collections' => ['records' => DB::table('charity_collections')->latest('collection_date')->paginate(20)],
        };

        return view('admin.administration-foundation', array_merge($data, [
            'section' => $section,
            'sections' => self::SECTIONS,
        ]));
    }

    public function store(Request $request, string $section)
    {
        abort_unless(isset(self::SECTIONS[$section]), 404);
        $data = match ($section) {
            'management' => $this->storeManagement($request),
            'documents' => $request->validate([
                'direction' => ['required', 'in:Masuk,Keluar'], 'document_number' => ['nullable', 'string', 'max:100'], 'document_date' => ['required', 'date'],
                'sender' => ['nullable', 'string', 'max:255'], 'recipient' => ['nullable', 'string', 'max:255'], 'subject' => ['required', 'string', 'max:255'], 'notes' => ['nullable', 'string', 'max:3000'],
            ]),
            'proposals' => $request->validate([
                'proposal_number' => ['nullable', 'string', 'max:100'], 'proposal_date' => ['required', 'date'], 'title' => ['required', 'string', 'max:255'], 'recipient' => ['nullable', 'string', 'max:255'], 'amount' => ['nullable', 'numeric', 'min:0'], 'proposal_type' => ['nullable', 'string', 'max:100'], 'content' => ['nullable', 'string'],
            ]),
            'qurban-animals' => $request->validate([
                'year' => ['required', 'integer', 'between:2000,2100'], 'animal_type' => ['required', 'in:Sapi,Kambing'], 'owner_name' => ['required', 'string', 'max:255'], 'participant_name' => ['nullable', 'string', 'max:255'], 'address' => ['nullable', 'string', 'max:500'], 'phone' => ['nullable', 'string', 'max:40'], 'price' => ['nullable', 'numeric', 'min:0'], 'payment_status' => ['required', 'in:Belum Lunas,Lunas'], 'notes' => ['nullable', 'string', 'max:2000'],
            ]),
            'qurban-distributions' => $request->validate([
                'year' => ['required', 'integer', 'between:2000,2100'], 'distribution_date' => ['required', 'date'], 'recipient_name' => ['required', 'string', 'max:255'], 'address' => ['nullable', 'string', 'max:500'], 'animal_type' => ['nullable', 'string', 'max:50'], 'quantity' => ['required', 'numeric', 'min:0'], 'notes' => ['nullable', 'string', 'max:2000'],
            ]),
            'ramadan-donors' => $request->validate([
                'year' => ['required', 'integer', 'between:2000,2100'], 'donation_date' => ['required', 'date'], 'name' => ['required', 'string', 'max:255'], 'address' => ['nullable', 'string', 'max:500'], 'amount' => ['required', 'numeric', 'min:0'], 'donation_type' => ['nullable', 'string', 'max:100'], 'notes' => ['nullable', 'string', 'max:2000'],
            ]),
            'charity-collections' => $request->validate([
                'year' => ['required', 'integer', 'between:2000,2100'], 'collection_date' => ['required', 'date'], 'amount' => ['required', 'numeric', 'min:0'], 'notes' => ['nullable', 'string', 'max:2000'],
            ]),
        };

        if ($section === 'management') {
            return redirect()->route('administration.foundation', ['section' => 'management'])->with('success', 'Data pengurus berhasil disimpan.');
        }

        $table = [
            'documents' => 'secretariat_documents', 'proposals' => 'proposals', 'qurban-animals' => 'qurban_animals',
            'qurban-distributions' => 'qurban_distributions', 'ramadan-donors' => 'ramadan_donors', 'charity-collections' => 'charity_collections',
        ][$section];
        if (in_array($section, ['documents', 'proposals'], true)) {
            $data['created_by'] = auth()->id();
        }
        $id = DB::table($table)->insertGetId(array_merge($data, ['created_at' => now(), 'updated_at' => now()]));
        $this->audit('created', $table, $id, $data);

        return redirect()->route('administration.foundation', ['section' => $section])->with('success', 'Data berhasil disimpan.');
    }

    private function storeManagement(Request $request): array
    {
        $type = $request->validate(['record_type' => ['required', 'in:period,position,member']])['record_type'];
        if ($type === 'period') {
            $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'starts_at' => ['nullable', 'date'], 'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'], 'status' => ['required', 'in:Draft,Aktif,Arsip']]);
            $id = DB::table('management_periods')->insertGetId(array_merge($data, ['created_at' => now(), 'updated_at' => now()]));
        } elseif ($type === 'position') {
            $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'sort_order' => ['nullable', 'integer', 'min:0']]);
            $id = DB::table('positions')->insertGetId(array_merge($data, ['created_at' => now(), 'updated_at' => now()]));
        } else {
            $data = $request->validate(['period_id' => ['required', 'exists:management_periods,id'], 'position_id' => ['nullable', 'exists:positions,id'], 'name' => ['required', 'string', 'max:255'], 'title' => ['nullable', 'string', 'max:100'], 'phone' => ['nullable', 'string', 'max:40'], 'address' => ['nullable', 'string', 'max:500'], 'sort_order' => ['nullable', 'integer', 'min:0']]);
            $id = DB::table('management_members')->insertGetId(array_merge($data, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]));
        }
        $this->audit('created', 'management_' . $type, $id, $data);
        return $data;
    }

    private function audit(string $action, string $type, int $id, array $data): void
    {
        DB::table('audit_logs')->insert(['user_id' => auth()->id(), 'action' => $action, 'auditable_type' => $type, 'auditable_id' => $id, 'metadata' => json_encode($data), 'ip_address' => request()->ip(), 'created_at' => now(), 'updated_at' => now()]);
    }
}
