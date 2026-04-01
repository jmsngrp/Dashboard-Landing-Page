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
        Schema::create('financial_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->integer('fiscal_year');
            $table->decimal('equity', 15, 2)->nullable();
            $table->decimal('net_assets', 15, 2)->nullable();
            $table->decimal('net_income_bs', 15, 2)->nullable();
            $table->decimal('staffing_budget', 15, 2)->nullable();
            $table->decimal('target_reserve', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['area_id', 'fiscal_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_snapshots');
    }
};
