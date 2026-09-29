<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('official_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('speaker_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('official_schedules');
    }
};