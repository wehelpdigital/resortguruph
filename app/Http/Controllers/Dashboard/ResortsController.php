<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\RgAuditLog;
use App\Models\RgResort;
use App\Models\RgResortMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResortsController extends Controller
{
    public function index()
    {
        $resorts = RgResort::where('owner_id', Auth::id())->orderByDesc('updated_at')->get();
        return view('dashboard.resorts.index', compact('resorts'));
    }

    public function create()
    {
        return view('dashboard.resorts.form', ['resort' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['owner_id'] = Auth::id();
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['status'] = 'draft';
        $data['amenities_json'] = $this->encodeAmenities($request);

        $resort = RgResort::create($data);
        $this->handleUploads($request, $resort);

        RgAuditLog::record('resort_created', ['target_type' => 'resort', 'target_id' => $resort->id]);
        return redirect()->route('dashboard.resorts.edit', $resort)
            ->with('flash', 'Resort created. Complete the details and submit for review.');
    }

    public function edit(RgResort $resort)
    {
        $this->authorize($resort);
        $resort->load('media');
        return view('dashboard.resorts.form', compact('resort'));
    }

    public function update(Request $request, RgResort $resort)
    {
        $this->authorize($resort);
        $data = $this->validateData($request, $resort);
        $data['amenities_json'] = $this->encodeAmenities($request);
        $resort->update($data);
        $this->handleUploads($request, $resort);
        RgAuditLog::record('resort_updated', ['target_type' => 'resort', 'target_id' => $resort->id]);
        return back()->with('flash', 'Resort saved.');
    }

    public function submitForReview(RgResort $resort)
    {
        $this->authorize($resort);
        if (!$resort->name || !$resort->description_html || !$resort->city) {
            return back()->withErrors(['form' => 'Complete name, description, and city before submitting for review.']);
        }
        $resort->update(['status' => 'pending_review']);
        RgAuditLog::record('resort_submitted_for_review', ['target_type' => 'resort', 'target_id' => $resort->id]);
        return back()->with('flash', 'Submitted for admin review. We will notify you once approved.');
    }

    public function destroy(RgResort $resort)
    {
        $this->authorize($resort);
        $resort->delete();
        RgAuditLog::record('resort_deleted', ['target_type' => 'resort', 'target_id' => $resort->id]);
        return redirect()->route('dashboard.resorts.index')->with('flash', 'Resort removed.');
    }

    public function uploadMedia(Request $request, RgResort $resort)
    {
        $this->authorize($resort);
        $request->validate(['file' => 'required|image|max:10240']);
        $path = $request->file('file')->store('resort-media/' . $resort->id, 'public');
        $sort = (int) RgResortMedia::where('resort_id', $resort->id)->max('sort_order') + 1;
        $m = RgResortMedia::create([
            'resort_id' => $resort->id,
            'kind' => 'image',
            'path' => $path,
            'sort_order' => $sort,
        ]);
        return response()->json(['ok' => true, 'id' => $m->id, 'url' => Storage::url($path)]);
    }

    public function deleteMedia(RgResort $resort, RgResortMedia $media)
    {
        $this->authorize($resort);
        if ($media->resort_id !== $resort->id) abort(404);
        Storage::disk('public')->delete($media->path);
        $media->delete();
        return response()->json(['ok' => true]);
    }

    private function authorize(RgResort $resort): void
    {
        if ($resort->owner_id !== Auth::id()) abort(403);
    }

    private function validateData(Request $request, ?RgResort $resort = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:500',
            'description_html' => 'nullable|string|max:30000',
            'region' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'phone' => 'nullable|string|max:64',
            'email' => 'nullable|email|max:191',
            'website' => 'nullable|url|max:255',
            'fb' => 'nullable|string|max:255',
            'ig' => 'nullable|string|max:255',
            'tt' => 'nullable|string|max:255',
            'price_range' => 'nullable|string|max:64',
            'capacity' => 'nullable|string|max:64',
            'primary_color' => 'nullable|string|max:16',
            'secondary_color' => 'nullable|string|max:16',
        ]);
    }

    private function encodeAmenities(Request $request): ?string
    {
        $amenities = $request->input('amenities', []);
        $custom = $request->input('amenities_custom');
        if ($custom) {
            foreach (preg_split('/,\s*/', $custom) as $a) {
                $a = trim($a);
                if ($a !== '') $amenities[] = $a;
            }
        }
        $amenities = array_values(array_unique(array_filter($amenities)));
        return $amenities ? json_encode($amenities) : null;
    }

    private function uniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $base = $slug;
        $i = 1;
        while (RgResort::where('slug', $slug)->exists()) {
            $slug = $base . '-' . (++$i);
        }
        return $slug;
    }

    private function handleUploads(Request $request, RgResort $resort): void
    {
        if ($request->hasFile('logo')) {
            $resort->update(['logo_path' => $request->file('logo')->store('resort-logos', 'public')]);
        }
        if ($request->hasFile('hero')) {
            $resort->update(['hero_path' => $request->file('hero')->store('resort-heros', 'public')]);
        }
    }
}
