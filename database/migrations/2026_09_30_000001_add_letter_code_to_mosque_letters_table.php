<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mosque_letters', function (Blueprint $table) {
            $table->string('letter_code', 50)->default('MNI')->after('letter_type');
        });
    }

    public function down(): void
    {
        Schema::table('mosque_letters', function (Blueprint $table) {
            $table->dropColumn('letter_code');
        });
    }
};