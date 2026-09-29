<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $legacySequences = DB::table('letter_sequences')->get();
        Schema::dropIfExists('letter_sequences');

        Schema::create('letter_sequences', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('letter_code', 50);
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
            $table->unique(['year', 'letter_code']);
        });

        foreach ($legacySequences as $legacySequence) {
            DB::table('letter_sequences')->insert([
                'year' => $legacySequence->year,
                'letter_code' => 'MNI',
                'last_number' => $legacySequence->last_number,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $existing2026 = DB::table('letter_sequences')->where('year', 2026)->where('letter_code', 'MNI')->value('last_number');
        DB::table('letter_sequences')->updateOrInsert(
            ['year' => 2026, 'letter_code' => 'MNI'],
            ['last_number' => max(2, (int) $existing2026), 'updated_at' => now(), 'created_at' => now()]
        );
    }

    public function down(): void
    {
        $lastNumbersByYear = DB::table('letter_sequences')
            ->select('year')
            ->selectRaw('MAX(last_number) as last_number')
            ->groupBy('year')
            ->get();

        Schema::dropIfExists('letter_sequences');
        Schema::create('letter_sequences', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->primary();
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
        });

        foreach ($lastNumbersByYear as $sequence) {
            DB::table('letter_sequences')->insert([
                'year' => $sequence->year,
                'last_number' => $sequence->last_number,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};