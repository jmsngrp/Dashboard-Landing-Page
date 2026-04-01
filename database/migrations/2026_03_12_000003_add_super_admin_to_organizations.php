<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add is_super_admin column if it doesn't exist
        if (!Schema::hasColumn('organizations', 'is_super_admin')) {
            DB::statement("ALTER TABLE organizations ADD COLUMN is_super_admin BOOLEAN NOT NULL DEFAULT FALSE");
        }

        // Seed "The Jamison Group" as super-admin org (or flag it if it already exists)
        $existing = DB::table('organizations')->where('name', 'The Jamison Group')->first();
        if ($existing) {
            DB::table('organizations')->where('id', $existing->id)->update(['is_super_admin' => true]);
        } else {
            DB::table('organizations')->insert([
                'name' => 'The Jamison Group',
                'is_active' => true,
                'is_super_admin' => true,
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('organizations', 'is_super_admin')) {
            DB::statement("ALTER TABLE organizations DROP COLUMN is_super_admin");
        }
    }
};
