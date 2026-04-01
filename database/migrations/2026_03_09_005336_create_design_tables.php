<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_presets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_system')->default(false);
            $table->json('settings');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('design_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('active_preset_id')->nullable()->constrained('design_presets')->nullOnDelete();
            $table->json('settings');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_settings');
        Schema::dropIfExists('design_presets');
    }
};
