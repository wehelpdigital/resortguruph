<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rg_seo_pages')) return;
        Schema::table('rg_seo_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('rg_seo_pages', 'tldr')) {
                $table->text('tldr')->nullable()->after('h1');
            }
            if (!Schema::hasColumn('rg_seo_pages', 'wwww_json')) {
                $table->json('wwww_json')->nullable()->after('tldr');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rg_seo_pages', function (Blueprint $table) {
            foreach (['tldr', 'wwww_json'] as $col) {
                if (Schema::hasColumn('rg_seo_pages', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
