<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)->default('operator')->after('password');
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('management_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->string('status', 30)->default('Draft');
            $table->timestamps();
        });

        Schema::create('management_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('management_periods')->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('address')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('photo_path')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('secretariat_documents', function (Blueprint $table) {
            $table->id();
            $table->string('direction', 20);
            $table->string('document_number')->nullable();
            $table->date('document_date');
            $table->string('sender')->nullable();
            $table->string('recipient')->nullable();
            $table->string('subject');
            $table->string('file_path')->nullable();
            $table->string('status', 30)->default('Aktif');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['direction', 'document_date']);
        });

        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->string('proposal_number')->nullable()->unique();
            $table->date('proposal_date');
            $table->string('title');
            $table->string('recipient')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('proposal_type')->nullable();
            $table->longText('content')->nullable();
            $table->string('status', 30)->default('Draft');
            $table->string('file_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('qurban_animals', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('animal_type');
            $table->string('owner_name');
            $table->string('participant_name')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 40)->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->string('payment_status', 30)->default('Belum Lunas');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['year', 'animal_type']);
        });

        Schema::create('qurban_distributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->date('distribution_date');
            $table->string('recipient_name');
            $table->string('address')->nullable();
            $table->string('animal_type')->nullable();
            $table->decimal('quantity', 10, 2)->default(0);
            $table->string('receipt_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('ramadan_donors', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->date('donation_date');
            $table->string('name');
            $table->string('address')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('donation_type')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('charity_collections', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->date('collection_date');
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('metadata')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('charity_collections');
        Schema::dropIfExists('ramadan_donors');
        Schema::dropIfExists('qurban_distributions');
        Schema::dropIfExists('qurban_animals');
        Schema::dropIfExists('proposals');
        Schema::dropIfExists('secretariat_documents');
        Schema::dropIfExists('management_members');
        Schema::dropIfExists('management_periods');
        Schema::dropIfExists('positions');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
