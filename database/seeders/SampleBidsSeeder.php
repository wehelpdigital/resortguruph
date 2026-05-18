<?php

namespace Database\Seeders;

use App\Models\RgKeyword;
use App\Models\RgListing;
use App\Models\RgOwner;
use App\Models\RgResort;
use App\Services\BiddingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampleBidsSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(BiddingService::class);

        // Pick top 5 keywords by traffic that have a published page
        $keywords = RgKeyword::query()
            ->whereHas('seoPages', fn($q) => $q->where('is_published', true))
            ->orderByDesc('search_volume_monthly')
            ->limit(5)
            ->get();

        $properties = RgResort::where('status', 'published')->get();
        if ($keywords->isEmpty() || $properties->isEmpty()) {
            $this->command->warn('No published keywords or properties available.');
            return;
        }

        $tiers = [
            // [bid_gp_to_add, label]
            ['bid' => 800, 'label' => 'Top spot (high roller)'],
            ['bid' => 350, 'label' => 'Mid bid'],
            ['bid' => 100, 'label' => 'Entry-level bid'],
        ];

        $totalListings = 0;
        $totalBids = 0;
        $now = now();

        foreach ($keywords as $keyword) {
            // Pick 3 distinct properties for this keyword (rotate so different keywords get different mixes)
            $selected = $properties->shuffle()->take(3);

            foreach ($selected as $idx => $property) {
                $owner = RgOwner::find($property->owner_id);
                if (!$owner) continue;

                // Skip if a listing already exists for this combo
                $existing = RgListing::where('keyword_id', $keyword->id)
                    ->where('resort_id', $property->id)
                    ->where('status', 'active')
                    ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', $now))
                    ->first();
                if ($existing) continue;

                $tier = $tiers[$idx] ?? ['bid' => 50, 'label' => 'Probe'];
                $basePrice = $service->basePriceFor($keyword);
                $totalNeeded = $basePrice + $tier['bid'];
                $balance = $service->ownerBalance($owner);

                // Top up owner's GP if not enough (admin grant for demo)
                if ($balance < $totalNeeded) {
                    $deficit = ($totalNeeded - $balance) + 500;
                    DB::table('rg_gp_ledger')->insert([
                        'owner_id' => $owner->id,
                        'amount' => $deficit,
                        'reason' => 'admin_adjustment',
                        'ref_type' => 'demo_seed',
                        'ref_id' => 0,
                        'status' => 'posted',
                        'meta_json' => json_encode(['note' => 'Sample bids seeder top-up']),
                        'created_at' => $now,
                    ]);
                }

                try {
                    $listing = $service->claimListing($owner, $keyword, $property);
                    $totalListings++;
                    if ($tier['bid'] > 0) {
                        $service->placeBid($owner, $listing, $tier['bid'], false);
                        $totalBids++;
                    }
                    $this->command->info(sprintf(
                        '  %-32s ← %-28s @ %d GP (%s)',
                        $keyword->slug,
                        $property->name,
                        $tier['bid'],
                        $tier['label']
                    ));
                } catch (\Throwable $e) {
                    $this->command->warn("  Skipped {$keyword->slug} / {$property->name}: {$e->getMessage()}");
                }
            }
        }

        $this->command->info("");
        $this->command->info("Total listings created: $totalListings");
        $this->command->info("Total bids placed: $totalBids");
    }
}
