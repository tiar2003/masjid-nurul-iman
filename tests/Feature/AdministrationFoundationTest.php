<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ZakatPeriod;
use App\Models\LetterTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdministrationFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_foundation_panel_and_proposal_storage_are_available(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('administration.foundation', ['section' => 'proposals']))
            ->assertOk()
            ->assertSee('Proposal');

        $this->assertDatabaseCount('management_members', 20);
        $this->assertDatabaseHas('management_members', ['name' => 'H. Sugeng Tiyarto', 'title' => 'SH. MH.']);
        $this->assertDatabaseHas('officials', ['name' => 'H. Sugeng Tiyarto', 'is_signatory' => 1]);

        ZakatPeriod::create([
            'year' => 2027,
            'hijri_year' => '1449 H',
            'fitrah_kg_per_person' => 3,
            'status' => 'Aktif',
        ]);
        LetterTemplate::where('template_key', 'ZAKAT_EDARAN_2025')->update([
            'is_active' => true,
            'is_verified' => true,
            'numbering_pattern' => '{seq3}/MNI/{month_roman}/{year}',
        ]);

        $this->actingAs($user)
            ->get(route('zakat-admin.index', ['year' => 2027, 'tab' => 'periode']))
            ->assertOk()
            ->assertSee('H. Sugeng Tiyarto')
            ->assertSee('Awaludin Gymnastiar')
            ->assertSee('Drs. H. Bambang Sutiyono');

        $this->actingAs($user)
            ->get(route('operations.reference-asset', 'kop-fix'))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');

        $this->actingAs($user)
            ->post(route('administration.foundation.store', 'proposals'), [
                'proposal_number' => 'PROP/001/2026',
                'proposal_date' => '2026-09-30',
                'title' => 'Renovasi Masjid',
                'recipient' => 'Donatur',
                'amount' => 1000000,
                'proposal_type' => 'Renovasi',
                'content' => 'Isi proposal',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('proposals', ['title' => 'Renovasi Masjid']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'created', 'auditable_type' => 'proposals']);
    }
}
