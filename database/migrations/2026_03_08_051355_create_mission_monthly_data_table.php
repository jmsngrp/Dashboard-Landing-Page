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
        Schema::create('mission_monthly_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->integer('fiscal_year');
            $table->integer('month'); // 1-12
            $table->integer('families')->nullable();
            $table->integer('individuals')->nullable();
            $table->integer('matched')->nullable();
            $table->integer('intake')->nullable();
            $table->integer('hosted_days')->nullable();
            $table->integer('hosted_nights')->nullable();
            $table->integer('volunteers')->nullable();
            $table->integer('active_volunteers')->nullable();
            $table->integer('partner_churches')->nullable();
            $table->integer('active_hosting')->nullable();
            $table->integer('active_friendships')->nullable();
            $table->integer('active_coaching')->nullable();
            $table->integer('graduations')->nullable();
            $table->timestamps();

            $table->unique(['area_id', 'fiscal_year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mission_monthly_data');
    }
};
