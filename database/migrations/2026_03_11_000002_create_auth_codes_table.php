<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('auth_codes')) return;
        Schema::create('auth_codes', function (Blueprint $table) {
            $table->id();
            $table->string('email', 320)->index();
            $table->string('code', 6);
            $table->timestampTz('expires_at');
            $table->boolean('used')->default(false);
            $table->timestampTz('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_codes');
    }
};
