<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

abstract class BulkContentBase extends Seeder
{
    abstract protected function pages(): array;

    public function run(): void
    {
        $now = now();
        $published = 0;
        $skipped = 0;

        foreach ($this->pages() as $slug => $data) {
            $kw = DB::table('rg_keywords')->where('slug', $slug)->first();
            if (!$kw) {
                $this->command->warn("Skipped: $slug (keyword not found)");
                continue;
            }
            $existing = DB::table('rg_seo_pages')->where('keyword_id', $kw->id)->first();
            if ($existing && $existing->is_published && stripos((string) $existing->body_html, 'Still finalizing') === false) {
                $skipped++;
                continue;
            }
            $payload = [
                'keyword_id' => $kw->id,
                'title' => $data['title'] ?? ucwords($kw->phrase),
                'meta_title' => $data['meta_title'],
                'meta_description' => $data['meta_description'],
                'h1' => $data['h1'],
                'intro_html' => $data['intro'],
                'body_html' => $data['body'],
                'faq_json' => json_encode($data['faqs']),
                'fallback_listing_html' => $data['fallback'],
                'is_published' => 1,
                'published_at' => $now,
                'updated_at' => $now,
            ];
            if ($existing) {
                DB::table('rg_seo_pages')->where('id', $existing->id)->update($payload);
            } else {
                $payload['created_at'] = $now;
                DB::table('rg_seo_pages')->insert($payload);
            }
            $published++;
        }
        $this->command->info(static::class . ": published $published  |  kept existing $skipped");
    }

    protected function build(string $h1, string $intro, string $body, array $faqs, ?string $metaDesc = null, ?string $metaTitle = null, ?string $fallback = null): array
    {
        return [
            'title' => $h1,
            'meta_title' => $metaTitle ?: ($h1 . ' | Resort Guru PH'),
            'meta_description' => $metaDesc ?: substr(strip_tags($intro), 0, 155),
            'h1' => $h1,
            'intro' => $intro,
            'body' => $body,
            'faqs' => $faqs,
            'fallback' => $fallback ?: '<p>We are still finalizing partner resorts for this destination. If you operate one, <a href="/register">sign up</a> to list your property here.</p>',
        ];
    }
}
