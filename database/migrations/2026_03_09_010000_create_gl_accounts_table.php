<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gl_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_number', 20);
            $table->string('account_name');
            $table->string('account_type', 20); // revenue, expense, other
            $table->foreignId('parent_account_id')
                  ->nullable()
                  ->constrained('gl_accounts')
                  ->nullOnDelete();
            $table->integer('depth')->default(0);
            $table->foreignId('pnl_line_item_id')
                  ->nullable()
                  ->constrained('pnl_line_items')
                  ->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('account_number');
            $table->index('pnl_line_item_id');
            $table->index('parent_account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gl_accounts');
    }
};
