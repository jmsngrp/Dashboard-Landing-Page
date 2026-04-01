<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->text('icon_base64')->nullable()->after('is_active');
            $table->string('primary_color', 7)->nullable()->after('icon_base64');
            $table->string('heading_font', 100)->nullable()->after('primary_color');
            $table->string('paragraph_font', 100)->nullable()->after('heading_font');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['icon_base64', 'primary_color', 'heading_font', 'paragraph_font']);
        });
    }
};
