<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('speakers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('title')->nullable();
            $table->enum('type', ['Khutbah', 'Kultum']);
            $table->string('skip_minggu')->nullable(); // Contoh: '3' atau '1,5'
            $table->string('skip_pasaran_jawa')->nullable(); // Contoh: 'Wage' (untuk Jumat Wage, dll)
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
        Schema::dropIfExists('speakers');
    }
};
