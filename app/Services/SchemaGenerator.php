<?php

namespace App\Services;

use App\Models\RgBlogPost;
use App\Models\RgKeyword;
use App\Models\RgListing;
use App\Models\RgResort;
use App\Models\RgSetting;
use Illuminate\Support\Collection;

class SchemaGenerator
{
    public function organization(): array
    {
        $name = RgSetting::get('site_name', 'Resort Guru PH');
        $url = config('app.url', url('/'));
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $name,
            'url' => $url,
            'logo' => url('/images/logo.png'),
            'sameAs' => array_values(array_filter([
                RgSetting::get('social_facebook'),
                RgSetting::get('social_instagram'),
                RgSetting::get('social_twitter'),
            ])),
        ];
    }

    public function website(): array
    {
        $url = config('app.url', url('/'));
        $name = RgSetting::get('site_name', 'Resort Guru PH');
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $name,
            'url' => $url,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => rtrim($url, '/') . '/destinations?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    public function breadcrumb(array $trail): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($trail)->values()->map(fn($t, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $t['name'],
                'item' => $t['url'],
            ])->all(),
        ];
    }

    public function faqPage(array $faqs): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => collect($faqs)->map(fn($f) => [
                '@type' => 'Question',
                'name' => $f['question'] ?? '',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => strip_tags($f['answer'] ?? ''),
                ],
            ])->values()->all(),
        ];
    }

    public function lodgingBusiness(RgResort $resort): array
    {
        $address = array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => $resort->address ?? null,
            'addressLocality' => $resort->city ?? null,
            'addressRegion' => $resort->province ?? null,
            'addressCountry' => 'PH',
        ]);

        $images = [];
        if ($resort->hero_path) $images[] = asset('storage/' . $resort->hero_path);
        if ($resort->logo_path) $images[] = asset('storage/' . $resort->logo_path);

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'LodgingBusiness',
            'name' => $resort->name,
            'description' => $resort->tagline ?: strip_tags($resort->description_html ?? ''),
            'url' => url('/listing/' . $resort->slug),
            'address' => $address,
            'telephone' => $resort->phone ?? null,
            'email' => $resort->email ?? null,
            'priceRange' => $resort->price_range ?? null,
        ];
        if ($images) $schema['image'] = $images;
        if ($resort->lat && $resort->lng) {
            $schema['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => (float) $resort->lat,
                'longitude' => (float) $resort->lng,
            ];
        }
        if (!empty($resort->amenities_json)) {
            $amenities = json_decode($resort->amenities_json, true) ?: [];
            if ($amenities) {
                $schema['amenityFeature'] = array_values(array_map(fn($a) => [
                    '@type' => 'LocationFeatureSpecification',
                    'name' => $a,
                    'value' => true,
                ], (array) $amenities));
            }
        }
        return array_filter($schema, fn($v) => $v !== null && $v !== '');
    }

    public function itemList(Collection $listings, RgKeyword $keyword): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => ucwords($keyword->phrase),
            'numberOfItems' => $listings->count(),
            'itemListElement' => $listings->values()->map(function ($l, $idx) {
                $r = $l->resort;
                if (!$r) return null;
                return [
                    '@type' => 'ListItem',
                    'position' => $idx + 1,
                    'item' => [
                        '@type' => 'LodgingBusiness',
                        'name' => $r->name,
                        'url' => url('/listing/' . $r->slug),
                        'image' => $r->hero_path ? asset('storage/' . $r->hero_path) : null,
                        'address' => array_filter([
                            '@type' => 'PostalAddress',
                            'addressLocality' => $r->city,
                            'addressRegion' => $r->province,
                            'addressCountry' => 'PH',
                        ]),
                    ],
                ];
            })->filter()->values()->all(),
        ];
    }

    /**
     * Article schema for keyword landing pages. Provides Google + AI engines with
     * author, publish/modify dates, image, and publisher info — material for the
     * E-E-A-T signals that drive Helpful Content + AI Overviews ranking.
     */
    public function article(\App\Models\RgSeoPage $page, ?\App\Models\RgAuthor $author, ?string $imageUrl, string $url): array
    {
        $org = $this->organization();
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $page->h1 ?: $page->title,
            'description' => $page->meta_description,
            'url' => $url,
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $url],
            'datePublished' => optional($page->published_at ?? $page->created_at)->toIso8601String(),
            'dateModified' => optional($page->updated_at)->toIso8601String(),
            'publisher' => [
                '@type' => 'Organization',
                'name' => $org['name'],
                'logo' => ['@type' => 'ImageObject', 'url' => $org['logo']],
            ],
            'inLanguage' => 'en-PH',
        ];
        if ($author) {
            $authorUrl = url('/author/' . $author->slug);
            $schema['author'] = array_filter([
                '@type' => 'Person',
                'name' => $author->name,
                'url' => $authorUrl,
                'image' => $author->avatarUrl(),
                'jobTitle' => $author->role ?: null,
                'description' => $author->bio ?: null,
                'sameAs' => array_values(array_filter([
                    $author->instagram ? 'https://instagram.com/' . ltrim($author->instagram, '@') : null,
                    $author->facebook ? (str_contains($author->facebook, 'http') ? $author->facebook : 'https://facebook.com/' . $author->facebook) : null,
                ])),
            ]);
        } else {
            $schema['author'] = ['@type' => 'Organization', 'name' => $org['name']];
        }
        if ($imageUrl) {
            $schema['image'] = $imageUrl;
        }
        return array_filter($schema, fn($v) => $v !== null && $v !== '' && $v !== []);
    }

    /**
     * AggregateRating + Review schema rolled into a TouristAttraction so the
     * stars surface on the SERP. Includes the individual reviews as Review
     * entities for richer rich-result eligibility.
     */
    public function aggregateRating(RgKeyword $keyword, \Illuminate\Support\Collection $reviews, string $url): array
    {
        $count = $reviews->count();
        $avg = $count > 0 ? round($reviews->avg('rating'), 2) : 0;
        $reviewItems = $reviews->map(function ($r) {
            return [
                '@type' => 'Review',
                'author' => ['@type' => 'Person', 'name' => $r->reviewer_name],
                'datePublished' => optional($r->review_date)->format('Y-m-d'),
                'reviewRating' => [
                    '@type' => 'Rating',
                    'ratingValue' => (int) $r->rating,
                    'bestRating' => 5,
                    'worstRating' => 1,
                ],
                'reviewBody' => $r->review_text,
            ];
        })->all();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'TouristAttraction',
            'name' => ucwords($keyword->phrase) . ' destinations',
            'url' => $url,
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => $avg,
                'reviewCount' => $count,
                'bestRating' => 5,
                'worstRating' => 1,
            ],
            'review' => $reviewItems,
        ];
    }

    public function blogPosting(RgBlogPost $post): array
    {
        $org = $this->organization();
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => $post->meta_description ?: $post->excerpt,
            'datePublished' => optional($post->published_at)->toIso8601String(),
            'dateModified' => optional($post->updated_at)->toIso8601String(),
            'url' => route('blog.show', $post->slug),
            'mainEntityOfPage' => route('blog.show', $post->slug),
            'publisher' => [
                '@type' => 'Organization',
                'name' => $org['name'],
                'logo' => ['@type' => 'ImageObject', 'url' => $org['logo']],
            ],
            'author' => ['@type' => 'Organization', 'name' => $org['name']],
        ];
        if ($post->cover_path) {
            $schema['image'] = asset('storage/' . $post->cover_path);
        }
        return array_filter($schema, fn($v) => $v !== null && $v !== '');
    }

    public function collectionPage(string $name, string $description, array $itemUrls): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $name,
            'description' => $description,
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => count($itemUrls),
                'itemListElement' => array_map(fn($u, $i) => [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'url' => $u,
                ], $itemUrls, array_keys($itemUrls)),
            ],
        ];
    }

    public function emit(array $schema): string
    {
        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }
}
