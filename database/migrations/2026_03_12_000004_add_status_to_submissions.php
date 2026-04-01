<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('submissions', 'status')) {
            return;
        }

        DB::statement("ALTER TABLE submissions ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE submissions ADD COLUMN updated_at TIMESTAMPTZ NULL");
        DB::statement("CREATE INDEX submissions_status_index ON submissions (status)");
    }

    public function down(): void
    {
        if (Schema::hasColumn('submissions', 'status')) {
            DB::statement("DROP INDEX IF EXISTS submissions_status_index");
            DB::statement("ALTER TABLE submissions DROP COLUMN status");
        }
        if (Schema::hasColumn('submissions', 'updated_at')) {
            DB::statement("ALTER TABLE submissions DROP COLUMN updated_at");
        }
    }
};
