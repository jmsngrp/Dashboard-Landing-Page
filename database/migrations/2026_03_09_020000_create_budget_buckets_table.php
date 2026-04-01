<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_buckets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');                    // revenue, cogs, program, admin
            $table->boolean('is_summary')->default(false); // computed totals
            $table->string('summary_formula')->nullable();  // e.g., "sum:revenue", "row:total_income - row:total_opex"
            $table->string('semantic_key')->nullable()->unique(); // 'total_income', 'net_income', etc.
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('legacy_pnl_line_item_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_buckets');
    }
};
