<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ramadan_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->enum('kind', ['Kultum', 'Takjil']);
            $table->date('date');
            $table->string('title')->nullable();
            $table->string('person_name')->nullable();
            $table->unsignedSmallInteger('quantity')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            $table->index(['year', 'kind', 'date']);
            $table->index(['is_public', 'date']);
        });

        Schema::create('qurban_committee_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('name');
            $table->string('position');
            $table->string('phone', 30)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['year', 'position']);
        });

        Schema::create('qurban_contributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->enum('animal_type', ['Sapi', 'Kambing']);
            $table->string('animal_group')->nullable();
            $table->string('participant_name');
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->date('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['year', 'animal_type', 'animal_group']);
        });

        Schema::create('qurban_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->enum('transaction_type', ['Pemasukan', 'Pengeluaran']);
            $table->date('date');
            $table->string('category');
            $table->string('description');
            $table->string('party_name')->nullable();
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['year', 'transaction_type', 'date']);
        });

        Schema::create('orphan_recipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('name');
            $table->string('guardian_name')->nullable();
            $table->string('group_name')->nullable();
            $table->text('private_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['year', 'is_active']);
        });

        Schema::create('orphan_donations', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->date('date');
            $table->string('donor_name');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->text('private_notes')->nullable();
            $table->timestamps();
            $table->index(['year', 'date']);
        });

        Schema::create('orphan_distributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->foreignId('recipient_id')->constrained('orphan_recipients')->restrictOnDelete();
            $table->date('date');
            $table->string('description');
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('private_notes')->nullable();
            $table->timestamps();
            $table->index(['year', 'date']);
        });

        Schema::create('letter_sequences', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->primary();
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
        });

        Schema::create('mosque_letters', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('sequence');
            $table->string('letter_number')->unique();
            $table->date('issue_date');
            $table->string('letter_type');
            $table->string('recipient');
            $table->string('subject');
            $table->longText('body');
            $table->enum('status', ['Draft', 'Terbit'])->default('Draft');
            $table->timestamps();
            $table->unique(['year', 'sequence']);
            $table->index(['year', 'issue_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mosque_letters');
        Schema::dropIfExists('letter_sequences');
        Schema::dropIfExists('orphan_distributions');
        Schema::dropIfExists('orphan_donations');
        Schema::dropIfExists('orphan_recipients');
        Schema::dropIfExists('qurban_transactions');
        Schema::dropIfExists('qurban_contributions');
        Schema::dropIfExists('qurban_committee_members');
        Schema::dropIfExists('ramadan_schedules');
    }
};