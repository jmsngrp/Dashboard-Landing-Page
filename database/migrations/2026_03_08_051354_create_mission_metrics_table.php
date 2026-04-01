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
        Schema::create('mission_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->integer('fiscal_year');
            $table->integer('families_served')->nullable();
            $table->integer('individuals_served')->nullable();
            $table->decimal('avg_monthly_families', 10, 2)->nullable();
            $table->integer('hosted_days')->nullable();
            $table->integer('hosted_nights')->nullable();
            $table->integer('total_hosted')->nullable();
            $table->integer('total_volunteers')->nullable();
            $table->integer('partner_churches')->nullable();
            $table->integer('service_hours')->nullable();
            $table->integer('intake')->nullable();
            $table->integer('opened')->nullable();
            $table->integer('graduations')->nullable();
            $table->integer('total_relationships')->nullable();
            $table->timestamps();

            $table->unique(['area_id', 'fiscal_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mission_metrics');
    }
};
