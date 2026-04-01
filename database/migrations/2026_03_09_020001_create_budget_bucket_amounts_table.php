<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_bucket_amounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_bucket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->integer('fiscal_year');
            $table->decimal('budget_amount', 15, 2)->nullable();
            $table->decimal('manual_actual', 15, 2)->nullable();
            $table->string('source')->default('manual'); // manual, gl_computed
            $table->timestamps();

            $table->unique(['budget_bucket_id', 'area_id', 'fiscal_year'], 'bucket_area_year_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_bucket_amounts');
    }
};
