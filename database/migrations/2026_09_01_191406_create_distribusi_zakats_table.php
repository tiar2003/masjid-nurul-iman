<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up(): void
{
    Schema::create('distribusi_zakats', function (Blueprint $table) {
        $table->id();
        $table->date('tanggal')->nullable();
        $table->string('nama');
        
        // 1. Zakat Fitrah
        $table->integer('fitrah_jiwa')->default(0);
        $table->decimal('fitrah_beras_kg', 8, 2)->default(0);
        $table->decimal('fitrah_uang_rp', 15, 2)->default(0);
        
        // 2. Zakat Mal
        $table->decimal('zakat_mal_rp', 15, 2)->default(0);
        
        // 3. Fidyah
        $table->integer('fidyah_jiwa')->default(0);
        $table->decimal('fidyah_beras_kg', 8, 2)->default(0);
        $table->decimal('fidyah_uang_rp', 15, 2)->default(0);
        
        // 4. Infaq & Shodaqoh
        $table->decimal('infaq_rp', 15, 2)->default(0);
        
        $table->text('keterangan')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distribusi_zakats');
    }
};
