<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_aliases', function (Blueprint $table) {
            $table->id();
            $table->string('alias_text');
            $table->foreignId('area_id')
                  ->constrained('areas')
                  ->cascadeOnDelete();
            $table->timestamps();

            $table->unique('alias_text');
            $table->index('area_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_aliases');
    }
};
