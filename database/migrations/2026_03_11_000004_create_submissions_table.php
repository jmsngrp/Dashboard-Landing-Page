<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('submissions')) return;
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id', 100);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('campaign_name', 300)->nullable();
            $table->date('deposit_date')->nullable();
            $table->integer('check_count')->nullable();
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->jsonb('payload')->nullable();
            $table->integer('webhook_status')->nullable();
            $table->timestampTz('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
