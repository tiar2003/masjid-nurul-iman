<?php

namespace Tests\Feature;

use App\Models\LetterTemplate;
use App\Models\MosqueLetter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Tests\TestCase;

class LetterTemplateGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_letter_template_generation_downloads_docx(): void
    {
        $template = LetterTemplate::create([
            'template_key' => 'TEST_TEMPLATE',
            'name' => 'Surat Test',
            'category' => 'Sekretariat',
            'version' => 1,
            'page_width_mm' => 210.00,
            'page_height_mm' => 297.00,
            'orientation' => 'portrait',
            'margins_mm' => ['top' => 10, 'right' => 20, 'bottom' => 10, 'left' => 20],
            'fields' => ['nomor_surat', 'tanggal', 'nama_penerima', 'perihal'],
            'numbering_key' => 'MNI',
            'numbering_pattern' => '{seq3}/MNI/I/{month_roman}/{year}',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('letter-template.preview', $template))
            ->assertOk()
            ->assertSee('{{nomor_surat}}');

        $this->actingAs($user)
            ->get(route('letter-template.generate-form', $template))
            ->assertOk();

        $this->actingAs($user)
            ->post(route('letter-template.generate', $template), [
                'nomor_surat' => '001',
                'tanggal' => '2026-09-30',
                'nama_penerima' => 'Bapak Ali',
                'perihal' => 'Permohonan bantuan',
                'body' => "Nomor: {{nomor_surat}}\nTanggal: {{tanggal}}\nKepada: {{nama_penerima}}\nPerihal: {{perihal}}",
            ])
            ->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $this->assertNotNull(LetterTemplate::find($template->id)->letters()->first()->docx_path);
        $this->assertNotNull(LetterTemplate::find($template->id)->letters()->first()->pdf_path);
        $letter = MosqueLetter::where('template_id', $template->id)->latest('id')->firstOrFail();
        $this->actingAs(User::factory()->create())
            ->get(route('operations.letters.docx', $letter))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $this->actingAs(User::factory()->create())
            ->get(route('operations.letters.pdf', $letter))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_generation_replaces_placeholders_in_original_docx_template(): void
    {
        $templatePath = storage_path('framework/testing/original-template.docx');
        if (! is_dir(dirname($templatePath))) {
            mkdir(dirname($templatePath), 0777, true);
        }

        $zip = new ZipArchive();
        $zip->open($templatePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('word/document.xml', '<w:document><w:body><w:p><w:r><w:t>{{nama_penerima}}</w:t></w:r></w:p><w:p><w:r><w:t>{{nama_</w:t></w:r><w:r><w:t>penerima}}</w:t></w:r></w:p></w:body></w:document>');
        $zip->close();

        $template = LetterTemplate::create([
            'template_key' => 'ORIGINAL_TEMPLATE',
            'name' => 'Template Asli',
            'category' => 'Sekretariat',
            'version' => 1,
            'source_path' => $templatePath,
            'page_width_mm' => 210.00,
            'page_height_mm' => 297.00,
            'orientation' => 'portrait',
            'margins_mm' => ['top' => 10, 'right' => 20, 'bottom' => 10, 'left' => 20],
            'fields' => ['nama_penerima'],
            'numbering_key' => 'MNI',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('letter-template.generate', $template), ['nama_penerima' => 'Bapak Ali']);

        $response->assertOk();
        $generatedPath = tempnam(sys_get_temp_dir(), 'generated_');
        file_put_contents($generatedPath, $response->getContent());
        $generatedZip = new ZipArchive();
        $generatedZip->open($generatedPath);
        $documentXml = $generatedZip->getFromName('word/document.xml');
        $generatedZip->close();
        unlink($generatedPath);

        $this->assertStringContainsString('Bapak Ali', $documentXml);
        $this->assertSame(2, substr_count($documentXml, 'Bapak Ali'));
        $this->assertStringNotContainsString('{{nama_penerima}}', $documentXml);
    }

    public function test_generation_can_use_a_real_docx_from_the_masjid_archive(): void
    {
        $sourcePath = 'MASJID/ROMADON/Zakat/EDARAN ZAKAT DAN TANDA TERIMA/SURAT EDARAN ZAKAT FITRAH.docx';
        $this->assertFileExists(storage_path('app/referensi-masjid/MASJID.zip'));

        $template = LetterTemplate::create([
            'template_key' => 'ARCHIVE_TEMPLATE',
            'name' => 'Template Arsip Resmi',
            'category' => 'Zakat',
            'version' => 1,
            'source_path' => $sourcePath,
            'page_width_mm' => 215.90,
            'page_height_mm' => 355.60,
            'orientation' => 'portrait',
            'margins_mm' => ['top' => 10, 'right' => 20, 'bottom' => 15, 'left' => 20],
            'fields' => ['recipient'],
            'numbering_key' => 'MNI',
            'numbering_pattern' => '{seq3}/MNI/{month_roman}/{year}',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $this->actingAs(User::factory()->create())
            ->post(route('letter-template.generate', $template), ['recipient' => 'Warga RW IX'])
            ->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    }
}
