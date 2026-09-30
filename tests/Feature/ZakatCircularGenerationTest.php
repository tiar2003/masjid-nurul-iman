<?php

namespace Tests\Feature;

use App\Models\LetterTemplate;
use App\Models\MosqueLetter;
use App\Models\Official;
use App\Models\User;
use App\Models\ZakatPeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ZakatCircularGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_zakat_circular_archives_docx_from_official_template(): void
    {
        $template = LetterTemplate::where('template_key', 'ZAKAT_EDARAN_2025')->firstOrFail();
        $template->update(['is_active' => true, 'is_verified' => true]);

        $period = ZakatPeriod::create([
            'year' => 2025,
            'hijri_year' => '1446 H',
            'starts_at' => '2025-03-01',
            'ends_at' => '2025-03-29',
            'fitrah_kg_per_person' => 3,
            'zakat_mal_rate_percent' => 2.5,
            'fidyah_kg_per_day' => 2.5,
            'fidyah_money_per_day' => 40000,
            'service_hours' => '20.00-22.00 WIB',
            'status' => 'Aktif',
        ]);

        $chairperson = Official::create(['name' => 'Ketua Masjid', 'position' => 'Ketua', 'is_signatory' => true, 'is_active' => true]);
        $secretary = Official::create(['name' => 'Sekretaris Masjid', 'position' => 'Sekretaris', 'is_signatory' => true, 'is_active' => true]);
        $knowing = Official::create(['name' => 'Ketua RW', 'position' => 'Mengetahui', 'is_signatory' => true, 'is_active' => true]);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('zakat-admin.circular.store', $period), [
                'issue_date' => '2025-03-10',
                'recipient' => 'Warga RW IX',
                'service_hours' => '20.00-22.00 WIB',
                'venue' => 'Masjid Nurul Iman',
                'closing_hijri_day' => '29 Ramadhan',
                'chairperson_id' => $chairperson->id,
                'secretary_id' => $secretary->id,
                'knowing_official_id' => $knowing->id,
                'cc' => "Ketua RW IX\nArsip",
            ]);

        $letter = MosqueLetter::latest('id')->firstOrFail();
        $response->assertRedirect(route('zakat-admin.circular.print', $letter));
        $this->assertNotNull($letter->docx_path);
        $this->assertNotNull($letter->pdf_path);
        $this->assertTrue(Storage::disk('local')->exists($letter->docx_path));
        $this->assertTrue(Storage::disk('local')->exists($letter->pdf_path));

        $docxPath = tempnam(sys_get_temp_dir(), 'zakat_docx_');
        file_put_contents($docxPath, Storage::disk('local')->get($letter->docx_path));
        $docx = new \ZipArchive();
        $docx->open($docxPath);
        $documentXml = $docx->getFromName('word/document.xml');
        $docx->close();
        unlink($docxPath);
        $this->assertStringContainsString('10 Maret 2025', $documentXml);
        $this->assertStringContainsString('1446 H', $documentXml);
        $this->assertStringContainsString('20.00-22.00 WIB', $documentXml);

        $this->actingAs(User::factory()->create())
            ->get(route('zakat-admin.circular.print', $letter))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }
}
