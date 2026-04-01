<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gl_accounts', function (Blueprint $table) {
            $table->foreignId('budget_bucket_id')
                ->nullable()
                ->after('pnl_line_item_id')
                ->constrained('budget_buckets')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('gl_accounts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('budget_bucket_id');
        });
    }
};
