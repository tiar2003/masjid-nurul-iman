<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zakat_periods', function (Blueprint $table) {
            $table->decimal('zakat_mal_rate_percent', 5, 2)->nullable()->after('fitrah_kg_per_person');
            $table->string('service_hours')->nullable()->after('fidyah_money_per_day');
        });

        Schema::table('mosque_letters', function (Blueprint $table) {
            $table->boolean('stamp_enabled')->default(false)->after('archived_at');
        });

        $patterns = [
            'ZAKAT_EDARAN_2025' => ['key' => 'ramadhan', 'pattern' => '{seq}/IX / Pan – Ramadhan / MNI / {year_short}'],
            'QURBAN_PANITIA_2026' => ['key' => 'idul-adha', 'pattern' => '{seq}/IX/Idul Adha/MNI/{year_short}'],
            'MOSQUE_OFFICIAL_LETTER' => ['key' => 'MNI', 'pattern' => '{seq3}/MNI/I/{month_roman}/{year}'],
        ];

        foreach ($patterns as $templateKey => $pattern) {
            DB::table('letter_templates')->where('template_key', $templateKey)->update([
                'numbering_key' => $pattern['key'],
                'numbering_pattern' => $pattern['pattern'],
                'updated_at' => now(),
            ]);
        }

        foreach ([
            [2025, 'ramadhan', 10],
            [2026, 'ramadhan', 11],
            [2025, 'phbi', 14],
            [2026, 'idul-adha', 16],
        ] as [$year, $code, $lastNumber]) {
            $existing = DB::table('letter_sequences')->where('year', $year)->where('letter_code', $code)->value('last_number');
            DB::table('letter_sequences')->updateOrInsert(
                ['year' => $year, 'letter_code' => $code],
                ['last_number' => max($lastNumber, (int) $existing), 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        Schema::table('mosque_letters', function (Blueprint $table) {
            $table->dropColumn('stamp_enabled');
        });
        Schema::table('zakat_periods', function (Blueprint $table) {
            $table->dropColumn(['zakat_mal_rate_percent', 'service_hours']);
        });
        DB::table('letter_templates')->whereIn('template_key', ['ZAKAT_EDARAN_2025', 'QURBAN_PANITIA_2026'])->update([
            'numbering_key' => null,
            'numbering_pattern' => null,
        ]);
        DB::table('letter_sequences')->whereIn('letter_code', ['ramadhan', 'phbi', 'idul-adha'])->delete();
    }
};