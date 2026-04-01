<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('highlight_kpis', function (Blueprint $table) {
            $table->id();
            $table->string('label');          // e.g. "Avg Families / Mo"
            $table->string('key');            // e.g. "avg_families"
            $table->string('type');           // mission, cost, fin
            $table->boolean('is_decimal')->default(false);
            $table->string('color_class')->default('green'); // green, accent, warm
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('highlight_groups', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('color')->default('green'); // CSS variable name
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('highlight_group_kpi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('highlight_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('highlight_kpi_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('highlight_group_kpi');
        Schema::dropIfExists('highlight_groups');
        Schema::dropIfExists('highlight_kpis');
    }
};
