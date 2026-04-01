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
        Schema::create('efficiency_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->integer('fiscal_year');

            // Cost per unit metrics (15,2)
            $table->decimal('cost_per_individual', 15, 2)->nullable();
            $table->decimal('cost_per_family', 15, 2)->nullable();
            $table->decimal('cost_per_hosted', 15, 2)->nullable();
            $table->decimal('cost_per_intake', 15, 2)->nullable();
            $table->decimal('cost_per_graduation', 15, 2)->nullable();
            $table->decimal('cost_per_service_hour', 15, 2)->nullable();
            $table->decimal('program_cost', 15, 2)->nullable();
            $table->decimal('revenue', 15, 2)->nullable();

            // Ratio metrics (8,4)
            $table->decimal('program_cost_ratio', 8, 4)->nullable();
            $table->decimal('admin_ratio', 8, 4)->nullable();
            $table->decimal('fundraising_roi', 8, 4)->nullable();
            $table->decimal('intake_conversion', 8, 4)->nullable();

            // Additional metrics
            $table->decimal('rev_per_volunteer', 15, 2)->nullable();
            $table->decimal('ind_per_10k_staff', 10, 2)->nullable();

            $table->timestamps();

            $table->unique(['area_id', 'fiscal_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('efficiency_metrics');
    }
};
