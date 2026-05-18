<?php

namespace App\Services;

use App\Models\RgAuditLog;
use App\Models\RgGpLedger;
use App\Models\RgKeyword;
use App\Models\RgListing;
use App\Models\RgListingBid;
use App\Models\RgOwner;
use App\Models\RgResort;
use App\Models\RgSetting;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BiddingService
{
    public function ownerBalance(RgOwner $owner): int
    {
        return (int) RgGpLedger::where('owner_id', $owner->id)
            ->where('status', 'posted')
            ->sum('amount');
    }

    public function basePriceFor(RgKeyword $keyword): int
    {
        $multiplier = (int) RgSetting::get('base_price_multiplier_per_1k_vol', 10);
        $minBase = (int) RgSetting::get('base_price_min_gp', 100);
        $derived = (int) round(($keyword->search_volume_monthly / 1000) * $multiplier);
        return max($minBase, $derived);
    }

    public function defaultDurationDays(): int
    {
        return (int) RgSetting::get('default_listing_duration_days', 30);
    }

    public function basePricePerDay(RgKeyword $keyword): float
    {
        $days = max(1, $this->defaultDurationDays());
        return $this->basePriceFor($keyword) / $days;
    }

    /**
     * Create a new active listing. Deducts base_gp from owner's ledger
     * inside a transaction that locks the keyword row to serialize concurrent claims.
     */
    public function claimListing(RgOwner $owner, RgKeyword $keyword, RgResort $resort): RgListing
    {
        if ($resort->owner_id !== $owner->id) {
            throw new RuntimeException('Resort does not belong to this owner.');
        }
        if ($resort->status !== 'published') {
            throw new RuntimeException('Resort must be published before claiming a listing slot.');
        }
        if (RgListing::where('keyword_id', $keyword->id)
            ->where('resort_id', $resort->id)
            ->where('status', 'active')
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->exists()) {
            throw new RuntimeException('This resort already has an active listing for this keyword.');
        }

        $basePrice = $this->basePriceFor($keyword);
        $durationDays = $this->defaultDurationDays();

        return DB::transaction(function () use ($owner, $keyword, $resort, $basePrice, $durationDays) {
            DB::table('rg_keywords')->where('id', $keyword->id)->lockForUpdate()->first();

            $balance = $this->ownerBalance($owner);
            if ($balance < $basePrice) {
                throw new RuntimeException("Insufficient Gold Points. Listing requires {$basePrice} GP, you have {$balance} GP.");
            }

            $now = now();
            $expiresAt = $now->copy()->addDays($durationDays);

            $listing = RgListing::create([
                'keyword_id' => $keyword->id,
                'resort_id' => $resort->id,
                'owner_id' => $owner->id,
                'base_gp' => $basePrice,
                'bid_gp' => 0,
                'starts_at' => $now,
                'expires_at' => $expiresAt,
                'last_bid_at' => $now,
                'status' => 'active',
            ]);

            RgGpLedger::create([
                'owner_id' => $owner->id,
                'amount' => -$basePrice,
                'reason' => 'listing_purchase',
                'ref_type' => 'rg_listings',
                'ref_id' => $listing->id,
                'status' => 'posted',
                'meta_json' => json_encode(['keyword' => $keyword->phrase, 'resort' => $resort->name, 'duration_days' => $durationDays]),
                'created_at' => $now,
            ]);

            RgListingBid::create([
                'listing_id' => $listing->id,
                'owner_id' => $owner->id,
                'keyword_id' => $keyword->id,
                'action' => 'claim',
                'gp_amount' => $basePrice,
                'bid_gp_after' => 0,
                'days_added' => $durationDays,
                'meta_json' => json_encode(['resort_id' => $resort->id]),
                'created_at' => $now,
            ]);

            RgAuditLog::record('listing_claimed', [
                'target_type' => 'rg_listings',
                'target_id' => $listing->id,
                'meta' => ['keyword_id' => $keyword->id, 'base_gp' => $basePrice],
            ]);

            return $listing;
        });
    }

    /**
     * Add GP to a listing's bid_gp. Optionally extends expires_at proportional
     * to base_price_per_day. Locks the listing row to serialize concurrent bids.
     */
    public function placeBid(RgOwner $owner, RgListing $listing, int $additionalGp, bool $extend = true): RgListing
    {
        if ($listing->owner_id !== $owner->id) {
            throw new RuntimeException('You can only bid on your own listings.');
        }
        if ($listing->status !== 'active') {
            throw new RuntimeException('This listing is not active.');
        }
        if ($additionalGp <= 0) {
            throw new RuntimeException('Bid amount must be positive.');
        }

        return DB::transaction(function () use ($owner, $listing, $additionalGp, $extend) {
            $listing = RgListing::lockForUpdate()->find($listing->id);
            $keyword = $listing->keyword()->first();

            $balance = $this->ownerBalance($owner);
            if ($balance < $additionalGp) {
                throw new RuntimeException("Insufficient Gold Points. You need {$additionalGp} GP, balance is {$balance} GP.");
            }

            $now = now();
            $previousMax = (int) RgListing::where('keyword_id', $listing->keyword_id)
                ->where('id', '<>', $listing->id)
                ->where('status', 'active')
                ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', $now))
                ->max('bid_gp');

            $daysAdded = 0;
            $listing->bid_gp += $additionalGp;
            $listing->last_bid_at = $now;
            if ($extend && $keyword) {
                $perDay = max(1, $this->basePricePerDay($keyword));
                $daysAdded = (int) floor($additionalGp / $perDay);
                if ($daysAdded > 0) {
                    $base = $listing->expires_at && $listing->expires_at->isFuture() ? $listing->expires_at : $now;
                    $listing->expires_at = $base->copy()->addDays($daysAdded);
                }
            }
            $listing->save();

            RgGpLedger::create([
                'owner_id' => $owner->id,
                'amount' => -$additionalGp,
                'reason' => 'bid',
                'ref_type' => 'rg_listings',
                'ref_id' => $listing->id,
                'status' => 'posted',
                'meta_json' => json_encode(['previous_max_bid' => $previousMax, 'new_bid_gp' => $listing->bid_gp, 'days_added' => $daysAdded]),
                'created_at' => $now,
            ]);

            RgListingBid::create([
                'listing_id' => $listing->id,
                'owner_id' => $owner->id,
                'keyword_id' => $listing->keyword_id,
                'action' => $extend && $daysAdded > 0 ? 'extend' : 'bid',
                'gp_amount' => $additionalGp,
                'bid_gp_after' => $listing->bid_gp,
                'days_added' => $daysAdded,
                'meta_json' => json_encode(['previous_max_other' => $previousMax]),
                'created_at' => $now,
            ]);

            if ($listing->bid_gp > $previousMax && $previousMax > 0) {
                RgAuditLog::record('listing_overtook_top', [
                    'target_type' => 'rg_listings',
                    'target_id' => $listing->id,
                    'meta' => ['new_bid_gp' => $listing->bid_gp, 'previous_max' => $previousMax],
                ]);
            }

            return $listing;
        });
    }

    /**
     * Returns quantized GP needed for this listing to take the top spot.
     * Returns 0 if listing is already at the top (including tie-broken winners).
     * Quantum prevents probe-and-bid attacks.
     */
    public function gpToTopHint(RgListing $listing): int
    {
        $now = now();
        $top = RgListing::where('keyword_id', $listing->keyword_id)
            ->where('status', 'active')
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', $now))
            ->orderByDesc('bid_gp')
            ->orderBy('last_bid_at')
            ->first();

        if (!$top || $top->id === $listing->id) return 0;

        $needed = $top->bid_gp > $listing->bid_gp
            ? ($top->bid_gp - $listing->bid_gp) + 1
            : 1;

        $quantum = (int) RgSetting::get('bid_top_hint_quantum_gp', 50);
        if ($quantum < 1) return $needed;
        return (int) (ceil($needed / $quantum) * $quantum);
    }

    public function activeListingsForKeyword(RgKeyword $keyword)
    {
        return RgListing::where('keyword_id', $keyword->id)
            ->where('status', 'active')
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->orderByDesc('bid_gp')
            ->orderBy('last_bid_at')
            ->get();
    }

    public function topBidForKeyword(RgKeyword $keyword): int
    {
        return (int) RgListing::where('keyword_id', $keyword->id)
            ->where('status', 'active')
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->max('bid_gp');
    }
}
