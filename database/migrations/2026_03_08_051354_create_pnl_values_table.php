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
        Schema::create('pnl_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('line_item_id')->constrained('pnl_line_items')->cascadeOnDelete();
            $table->integer('fiscal_year');
            $table->decimal('amount', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['area_id', 'line_item_id', 'fiscal_year']);
            $table->index(['area_id', 'fiscal_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pnl_values');
    }
};
