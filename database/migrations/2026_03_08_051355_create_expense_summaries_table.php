<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expense_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->integer('fiscal_year');
            $table->decimal('program', 15, 2)->nullable();
            $table->decimal('admin', 15, 2)->nullable();
            $table->decimal('fundraising', 15, 2)->nullable();
            $table->decimal('total', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['area_id', 'fiscal_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_summaries');
    }
};
