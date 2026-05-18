<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rg_seo_pages', function (Blueprint $t) {
            if (!Schema::hasColumn('rg_seo_pages', 'slug')) {
                $t->string('slug', 200)->nullable()->after('keyword_id');
            }
            if (!Schema::hasColumn('rg_seo_pages', 'is_primary')) {
                $t->boolean('is_primary')->default(false)->after('is_published');
            }
            if (!Schema::hasColumn('rg_seo_pages', 'robots')) {
                $t->string('robots', 50)->nullable()->after('canonical_url');
            }
        });

        // Drop the unique constraint on keyword_id so multiple pages per keyword are allowed
        try {
            DB::statement('ALTER TABLE rg_seo_pages DROP INDEX rg_seo_pages_keyword_id_unique');
        } catch (\Throwable $e) {
            // index may already be gone or named differently — non-fatal
        }

        // Backfill: each existing page gets its keyword's slug
        DB::statement('UPDATE rg_seo_pages p
            JOIN rg_keywords k ON k.id = p.keyword_id
            SET p.slug = k.slug
            WHERE p.slug IS NULL OR p.slug = ""');

        // Mark all existing pages as primary (they were 1:1 originally)
        DB::table('rg_seo_pages')->update(['is_primary' => 1]);

        // Add unique index on slug (after backfill)
        Schema::table('rg_seo_pages', function (Blueprint $t) {
            $t->unique('slug', 'rg_seo_pages_slug_unique');
            $t->index('keyword_id', 'rg_seo_pages_keyword_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('rg_seo_pages', function (Blueprint $t) {
            try { $t->dropUnique('rg_seo_pages_slug_unique'); } catch (\Throwable $e) {}
            try { $t->dropIndex('rg_seo_pages_keyword_id_index'); } catch (\Throwable $e) {}
            if (Schema::hasColumn('rg_seo_pages', 'robots')) $t->dropColumn('robots');
            if (Schema::hasColumn('rg_seo_pages', 'is_primary')) $t->dropColumn('is_primary');
            if (Schema::hasColumn('rg_seo_pages', 'slug')) $t->dropColumn('slug');
        });
    }
};
