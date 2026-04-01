<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qbo_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('realm_id');
            $table->text('access_token');
            $table->text('refresh_token');
            $table->timestamp('access_token_expires_at');
            $table->timestamp('refresh_token_expires_at');
            $table->string('company_name')->nullable();
            $table->foreignId('connected_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table('gl_accounts', function (Blueprint $table) {
            $table->string('qbo_account_id', 50)->nullable()->after('account_number');
            $table->index('qbo_account_id');
        });

        Schema::table('gl_imports', function (Blueprint $table) {
            $table->string('source', 20)->default('xlsx_upload')->after('filename');
            $table->date('sync_start_date')->nullable()->after('fiscal_year');
            $table->date('sync_end_date')->nullable()->after('sync_start_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qbo_tokens');

        Schema::table('gl_accounts', function (Blueprint $table) {
            $table->dropIndex(['qbo_account_id']);
            $table->dropColumn('qbo_account_id');
        });

        Schema::table('gl_imports', function (Blueprint $table) {
            $table->dropColumn(['source', 'sync_start_date', 'sync_end_date']);
        });
    }
};
