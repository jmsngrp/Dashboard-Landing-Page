<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gl_imports', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->integer('fiscal_year');
            $table->integer('total_rows')->default(0);
            $table->integer('matched_rows')->default(0);
            $table->integer('unmatched_rows')->default(0);
            $table->integer('new_accounts')->default(0);
            $table->string('status', 20)->default('pending'); // pending, processing, completed, failed
            $table->text('error_log')->nullable();
            $table->foreignId('imported_by')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->timestamps();

            $table->index('fiscal_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gl_imports');
    }
};
