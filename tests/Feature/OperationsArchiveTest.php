<?php

namespace Tests\Feature;

use App\Models\MosqueLetter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationsArchiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_letters_can_be_searched_and_archived_without_deletion(): void
    {
        $user = User::factory()->create();
        $visible = MosqueLetter::create([
            'year' => 2026,
            'sequence' => 1,
            'letter_code' => 'MNI',
            'letter_number' => '001/MNI/IX/2026',
            'issue_date' => '2026-09-30',
            'letter_type' => 'Surat Permohonan',
            'recipient' => 'Bapak Ali',
            'subject' => 'Bantuan renovasi',
            'body' => 'Isi surat',
            'status' => 'Draft',
        ]);
        MosqueLetter::create([
            'year' => 2026,
            'sequence' => 2,
            'letter_code' => 'MNI',
            'letter_number' => '002/MNI/IX/2026',
            'issue_date' => '2026-09-30',
            'letter_type' => 'Surat Undangan',
            'recipient' => 'Panitia',
            'subject' => 'Rapat',
            'body' => 'Isi surat',
            'status' => 'Draft',
            'archived_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('operations.index', ['module' => 'letters', 'year' => 2026, 'q' => 'renovasi']))
            ->assertOk()
            ->assertSee('Bapak Ali')
            ->assertDontSee('Surat Undangan');

        $this->actingAs($user)
            ->delete(route('operations.destroy', ['module' => 'letters', 'id' => $visible->id]), ['year' => 2026])
            ->assertRedirect();

        $this->assertDatabaseHas('mosque_letters', [
            'id' => $visible->id,
        ]);
        $this->assertNotNull(MosqueLetter::find($visible->id)->archived_at);
    }
}
