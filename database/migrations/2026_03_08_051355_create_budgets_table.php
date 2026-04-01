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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->integer('fiscal_year');
            $table->decimal('revenue', 15, 2)->nullable();
            $table->decimal('individual_donations', 15, 2)->nullable();
            $table->decimal('church_giving', 15, 2)->nullable();
            $table->decimal('grant_revenue', 15, 2)->nullable();
            $table->decimal('foundation_revenue', 15, 2)->nullable();
            $table->decimal('fundraising_events', 15, 2)->nullable();
            $table->decimal('institutional', 15, 2)->nullable();
            $table->decimal('cogs', 15, 2)->nullable();
            $table->decimal('program_costs', 15, 2)->nullable();
            $table->decimal('admin_costs', 15, 2)->nullable();
            $table->decimal('total_expenses', 15, 2)->nullable();
            $table->decimal('gross_profit', 15, 2)->nullable();
            $table->decimal('net_operating', 15, 2)->nullable();
            $table->decimal('rev_sharing', 15, 2)->nullable();
            $table->decimal('net_revenue', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['area_id', 'fiscal_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
