<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mosque_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique();
            $table->longText('setting_value')->nullable();
            $table->timestamps();
        });

        Schema::create('officials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('position');
            $table->string('signature_path')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_signatory')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('letter_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_key', 100);
            $table->string('name');
            $table->string('category', 100);
            $table->unsignedSmallInteger('version')->default(1);
            $table->string('source_path')->nullable();
            $table->decimal('page_width_mm', 7, 2);
            $table->decimal('page_height_mm', 7, 2);
            $table->enum('orientation', ['portrait', 'landscape'])->default('portrait');
            $table->json('margins_mm');
            $table->json('fields');
            $table->string('numbering_key', 100)->nullable();
            $table->string('numbering_pattern', 255)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->unique(['template_key', 'version']);
            $table->index(['category', 'is_active']);
        });

        Schema::create('zakat_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year')->unique();
            $table->string('hijri_year', 12)->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->decimal('fitrah_kg_per_person', 6, 2)->nullable();
            $table->decimal('fidyah_kg_per_day', 6, 2)->nullable();
            $table->decimal('fidyah_money_per_day', 15, 2)->nullable();
            $table->enum('status', ['Draft', 'Aktif', 'Ditutup'])->default('Draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('zakat_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('zakat_periods')->cascadeOnDelete();
            $table->string('receipt_number', 50)->nullable()->unique();
            $table->date('date');
            $table->string('muzakki_name');
            $table->string('address')->nullable();
            $table->unsignedSmallInteger('souls')->default(0);
            $table->decimal('fitrah_rice_kg', 9, 2)->default(0);
            $table->decimal('fitrah_money', 15, 2)->default(0);
            $table->decimal('zakat_mal', 15, 2)->default(0);
            $table->unsignedSmallInteger('fidyah_days')->default(0);
            $table->decimal('fidyah_rice_kg', 9, 2)->default(0);
            $table->decimal('fidyah_money', 15, 2)->default(0);
            $table->decimal('infaq', 15, 2)->default(0);
            $table->decimal('shodaqoh', 15, 2)->default(0);
            $table->string('officer_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['period_id', 'date']);
            $table->index(['period_id', 'muzakki_name']);
        });

        Schema::create('zakat_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('zakat_periods')->cascadeOnDelete();
            $table->string('distribution_number', 50)->nullable();
            $table->date('date');
            $table->string('recipient_name');
            $table->string('address')->nullable();
            $table->string('asnaf')->nullable();
            $table->decimal('fitrah_rice_kg', 9, 2)->default(0);
            $table->decimal('fitrah_money', 15, 2)->default(0);
            $table->decimal('zakat_mal', 15, 2)->default(0);
            $table->decimal('fidyah_rice_kg', 9, 2)->default(0);
            $table->decimal('fidyah_money', 15, 2)->default(0);
            $table->decimal('infaq', 15, 2)->default(0);
            $table->decimal('shodaqoh', 15, 2)->default(0);
            $table->enum('status', ['Belum Disalurkan', 'Sudah Disalurkan'])->default('Belum Disalurkan');
            $table->date('distributed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['period_id', 'date']);
            $table->index(['period_id', 'status']);
        });

        Schema::create('zakat_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('zakat_periods')->cascadeOnDelete();
            $table->string('report_type', 100);
            $table->json('summary');
            $table->string('file_path')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['period_id', 'report_type']);
        });

        Schema::table('mosque_letters', function (Blueprint $table) {
            $table->foreignId('template_id')->nullable()->after('id')->constrained('letter_templates')->nullOnDelete();
            $table->json('field_data')->nullable()->after('body');
            $table->string('event_name')->nullable()->after('subject');
            $table->string('signed_by')->nullable()->after('event_name');
            $table->string('pdf_path')->nullable()->after('field_data');
            $table->timestamp('archived_at')->nullable()->after('status');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });

        $settings = [
            'mosque_name' => 'MASJID NURUL IMAN',
            'mosque_address' => 'RW. IX Kelurahan Krapyak Semarang',
            'secretariat_address' => 'Jl. Hanoman IX No. 30 Semarang',
            'mosque_phone' => '081325149999',
            'letterhead_asset' => 'MASJID/KOP FIX.png',
            'stamp_asset' => 'MASJID/cap_msjid_fix-removebg-preview.png',
        ];
        foreach ($settings as $key => $value) {
            DB::table('mosque_settings')->insert([
                'setting_key' => $key,
                'setting_value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $templates = [
            ['ZAKAT_EDARAN_2025', 'Surat Edaran Zakat Fitrah', 'Zakat', 'MASJID/FILE MAS ARIF/LAPORAN ZAKAT FITRAH/EDARAN ZAKAT DAN TANDA TERIMA/SURAT EDARAN ZAKAT FITRAH.docx', 215.90, 355.60, 'portrait', ['top' => 10, 'right' => 20, 'bottom' => 15, 'left' => 20], ['issue_date', 'letter_number', 'attachment', 'recipient', 'hijri_year', 'masehi_year', 'fitrah_kg', 'fidyah_kg', 'fidyah_money', 'service_hours', 'closing_date', 'venue', 'officials', 'cc'], 'ramadhan', false],
            ['ZAKAT_FINAL_2025', 'Hasil Akhir Zakat Fitrah', 'Zakat', 'MASJID/FILE MAS ARIF/LAPORAN ZAKAT FITRAH/EDARAN ZAKAT DAN TANDA TERIMA/HASIL AKHIR ZAKAT FITRAH.docx', 215.90, 355.60, 'portrait', ['top' => 42.51, 'right' => 25.40, 'bottom' => 25.40, 'left' => 25.40], ['hijri_year', 'masehi_year', 'receipt_summary', 'distribution_summary', 'distribution_dates', 'recipients'], 'zakat-report', false],
            ['ZAKAT_HANDOVER_2025', 'Tanda Penyerahan dan Tanda Terima Zakat', 'Zakat', 'MASJID/FILE MAS ARIF/LAPORAN ZAKAT FITRAH/EDARAN ZAKAT DAN TANDA TERIMA/TANDA PENYERAHAN ZAKAT FITRAH.docx', 330.00, 215.90, 'landscape', ['top' => 10, 'right' => 10, 'bottom' => 7.5, 'left' => 10], ['date', 'giver', 'receiver', 'fitrah_rice_kg', 'fitrah_souls', 'fitrah_money', 'mal_items', 'fidyah_rice_kg', 'fidyah_money', 'infaq_items', 'shodaqoh_items'], 'zakat-receipt', false],
            ['UPZ_PROPOSAL', 'Surat Pengusulan UPZ Masjid', 'Sekretariat', 'MASJID/Seketetariat/Surat Pengusulan UPZ Masjid.docx', 215.90, 355.60, 'portrait', ['top' => 5, 'right' => 17, 'bottom' => 5, 'left' => 22.5], ['issue_date', 'letter_number', 'recipient', 'subject', 'body', 'signed_by'], 'upz', false],
            ['RAMADAN_TAKJIL', 'Jadwal Takjil Ramadan', 'Kegiatan', 'MASJID/ROMADON/JADWAL TAKJIL MASJID NURUL IMAN RAMADHAN.docx', 215.90, 355.60, 'portrait', ['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20], ['year', 'entries'], 'ramadhan', false],
            ['RAMADAN_KULTUM_2025', 'Jadwal Kultum Ramadan', 'Kegiatan', 'MASJID/ROMADON/KULTUM & JADWAL/Jadwal Kultum 2025.docx', 215.90, 355.60, 'portrait', ['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20], ['year', 'entries'], 'ramadhan', false],
            ['QURBAN_PANITIA_2026', 'Panitia Idul Adha 2026', 'Qurban', 'MASJID/QURBAN/PANITIA IDUL ADHA 26 - Google Sheets.docx', 215.90, 355.60, 'portrait', ['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20], ['year', 'committee', 'assignments'], 'idul-adha', false],
            ['QURBAN_UNDANGAN_RAPAT', 'Surat Undangan Rapat Qurban', 'Qurban', 'MASJID/QURBAN/PAKET SURAT QURBAN/SURAT UNDANGAN RAPAT QURBAN.docx', 215.90, 355.60, 'portrait', ['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20], ['issue_date', 'letter_number', 'recipient', 'meeting_date', 'time', 'venue', 'agenda', 'signed_by'], 'idul-adha', false],
            ['MOSQUE_OFFICIAL_LETTER', 'Surat Pengurus Masjid', 'Sekretariat', 'MASJID/Seketetariat/Draft Surat Pengurus Masjid.docx', 210.00, 297.00, 'portrait', ['top' => 7.5, 'right' => 25.4, 'bottom' => 25.4, 'left' => 25.4], ['issue_date', 'letter_number', 'recipient', 'subject', 'body', 'signed_by'], 'mni', false],
        ];

        foreach ($templates as [$key, $name, $category, $sourcePath, $width, $height, $orientation, $margins, $fields, $numberingKey, $active]) {
            DB::table('letter_templates')->insert([
                'template_key' => $key,
                'name' => $name,
                'category' => $category,
                'version' => 1,
                'source_path' => $sourcePath,
                'page_width_mm' => $width,
                'page_height_mm' => $height,
                'orientation' => $orientation,
                'margins_mm' => json_encode($margins),
                'fields' => json_encode($fields),
                'numbering_key' => $numberingKey,
                'numbering_pattern' => null,
                'is_verified' => false,
                'is_active' => $active,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('mosque_letters', function (Blueprint $table) {
            $table->dropConstrainedForeignId('template_id');
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn(['field_data', 'event_name', 'signed_by', 'pdf_path', 'archived_at']);
        });

        Schema::dropIfExists('zakat_reports');
        Schema::dropIfExists('zakat_distributions');
        Schema::dropIfExists('zakat_receipts');
        Schema::dropIfExists('zakat_periods');
        Schema::dropIfExists('letter_templates');
        Schema::dropIfExists('officials');
        Schema::dropIfExists('mosque_settings');
    }
};