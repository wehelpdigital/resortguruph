<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\RgKeyword;
use App\Models\RgListing;
use App\Models\RgResort;
use App\Services\BiddingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class ListingsController extends Controller
{
    public function __construct(private BiddingService $bidding)
    {
    }

    public function index()
    {
        $owner = Auth::user();
        $listings = RgListing::where('owner_id', $owner->id)
            ->where('status', 'active')
            ->with(['keyword', 'resort'])
            ->orderByDesc('last_bid_at')
            ->get();

        $listings->each(function ($l) {
            $l->gp_to_top = $this->bidding->gpToTopHint($l);
            $l->is_at_top = $l->gp_to_top === 0;
        });

        $balance = $this->bidding->ownerBalance($owner);
        $publishedResortCount = RgResort::where('owner_id', $owner->id)->where('status', 'published')->count();

        return view('dashboard.listings.index', compact('listings', 'balance', 'publishedResortCount'));
    }

    public function browse(Request $request)
    {
        $search = trim((string) $request->get('q', ''));
        $cluster = trim((string) $request->get('cluster', ''));

        $query = RgKeyword::query()
            ->where('status', 'active')
            ->whereHas('seoPage', fn($q) => $q->where('is_published', true));

        if ($search !== '') {
            $query->where('phrase', 'LIKE', "%{$search}%");
        }
        if ($cluster !== '') {
            $query->where('cluster_tag', $cluster);
        }

        $keywords = $query->orderByDesc('search_volume_monthly')->paginate(20)->withQueryString();
        $clusters = RgKeyword::select('cluster_tag')->distinct()->orderBy('cluster_tag')->pluck('cluster_tag')->filter()->values();

        $keywords->each(function ($k) {
            $k->base_price = $this->bidding->basePriceFor($k);
            $k->top_bid = $this->bidding->topBidForKeyword($k);
            $k->active_listings = RgListing::where('keyword_id', $k->id)->where('status', 'active')->count();
        });

        $balance = $this->bidding->ownerBalance(Auth::user());

        return view('dashboard.listings.browse', compact('keywords', 'clusters', 'search', 'cluster', 'balance'));
    }

    public function claimForm(Request $request, RgKeyword $keyword)
    {
        $owner = Auth::user();
        $resorts = RgResort::where('owner_id', $owner->id)
            ->where('status', 'published')
            ->orderBy('name')
            ->get();

        $alreadyListed = RgListing::where('keyword_id', $keyword->id)
            ->where('owner_id', $owner->id)
            ->where('status', 'active')
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->pluck('resort_id')
            ->all();

        $balance = $this->bidding->ownerBalance($owner);
        $basePrice = $this->bidding->basePriceFor($keyword);
        $duration = $this->bidding->defaultDurationDays();

        return view('dashboard.listings.claim', compact('keyword', 'resorts', 'alreadyListed', 'balance', 'basePrice', 'duration'));
    }

    public function claim(Request $request, RgKeyword $keyword)
    {
        $data = $request->validate([
            'resort_id' => 'required|integer|exists:rg_resorts,id',
        ]);
        $owner = Auth::user();
        $resort = RgResort::findOrFail($data['resort_id']);

        try {
            $listing = $this->bidding->claimListing($owner, $keyword, $resort);
        } catch (Throwable $e) {
            return back()->withErrors(['claim' => $e->getMessage()])->withInput();
        }
        return redirect()->route('dashboard.listings.index')
            ->with('flash', "Listing claimed for {$keyword->phrase}. {$resort->name} is now live.");
    }

    public function bid(Request $request, RgListing $listing)
    {
        $data = $request->validate([
            'gp_amount' => 'required|integer|min:1',
            'extend' => 'nullable',
        ]);
        $owner = Auth::user();
        $extend = (bool) ($data['extend'] ?? true);

        try {
            $this->bidding->placeBid($owner, $listing, (int) $data['gp_amount'], $extend);
        } catch (Throwable $e) {
            return back()->withErrors(['bid' => $e->getMessage()])->withInput();
        }
        return redirect()->route('dashboard.listings.index')->with('flash', "Bid added: +{$data['gp_amount']} GP.");
    }
}
