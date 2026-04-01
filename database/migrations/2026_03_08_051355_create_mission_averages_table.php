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
        Schema::create('mission_averages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->integer('fiscal_year');

            // Average decimals (10,2)
            $table->decimal('avg_families', 10, 2)->nullable();
            $table->decimal('avg_individuals', 10, 2)->nullable();
            $table->decimal('avg_volunteers', 10, 2)->nullable();
            $table->decimal('avg_active_volunteers', 10, 2)->nullable();
            $table->decimal('avg_partner_churches', 10, 2)->nullable();
            $table->decimal('avg_hosting', 10, 2)->nullable();
            $table->decimal('avg_friendships', 10, 2)->nullable();
            $table->decimal('avg_coaching', 10, 2)->nullable();
            $table->decimal('avg_relationships', 10, 2)->nullable();

            // Totals (integers)
            $table->integer('unique_families')->nullable();
            $table->integer('unique_individuals')->nullable();
            $table->integer('total_intake')->nullable();
            $table->integer('total_opened')->nullable();
            $table->integer('total_matched')->nullable();
            $table->integer('total_graduations')->nullable();
            $table->integer('total_hosted_days')->nullable();
            $table->integer('total_hosted_nights')->nullable();
            $table->integer('total_hosted')->nullable();
            $table->integer('total_service_hours')->nullable();

            // December snapshot values (integers)
            $table->integer('dec_families')->nullable();
            $table->integer('dec_individuals')->nullable();
            $table->integer('dec_volunteers')->nullable();
            $table->integer('dec_active_volunteers')->nullable();
            $table->integer('dec_partner_churches')->nullable();
            $table->integer('dec_hosting')->nullable();
            $table->integer('dec_friendships')->nullable();
            $table->integer('dec_coaching')->nullable();
            $table->integer('dec_relationships')->nullable();

            // Ratio
            $table->decimal('ind_fam_ratio', 8, 2)->nullable();

            $table->timestamps();

            $table->unique(['area_id', 'fiscal_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mission_averages');
    }
};
