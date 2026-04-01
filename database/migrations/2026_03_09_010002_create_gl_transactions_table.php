<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gl_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gl_account_id')
                  ->constrained('gl_accounts')
                  ->cascadeOnDelete();
            $table->foreignId('gl_import_id')
                  ->constrained('gl_imports')
                  ->cascadeOnDelete();
            $table->foreignId('area_id')
                  ->nullable()
                  ->constrained('areas')
                  ->nullOnDelete();
            $table->integer('fiscal_year');
            $table->date('transaction_date');
            $table->string('type', 50)->nullable();
            $table->string('num', 50)->nullable();
            $table->string('name')->nullable();
            $table->text('memo')->nullable();
            $table->string('split_account')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('balance', 15, 2)->nullable();
            $table->string('memo_area_raw')->nullable();
            $table->timestamps();

            $table->index(['gl_account_id', 'fiscal_year']);
            $table->index(['area_id', 'fiscal_year']);
            $table->index('fiscal_year');
            $table->index('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gl_transactions');
    }
};
