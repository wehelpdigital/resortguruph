<?php

namespace App\Services;

/**
 * Inline SVG icon catalog used by BlogContentEnhancer to decorate H2 headings
 * and figure captions. Heroicons (outline) 20x20, MIT licensed. Keyed by
 * semantic intent rather than icon name so the matcher can pick the right
 * icon from a heading without coupling to a specific drawing.
 */
class IconLibrary
{
    public static function get(string $key, string $extraClass = ''): string
    {
        $svg = self::svgFor($key);
        if ($svg === null) return '';
        $cls = trim('w-5 h-5 inline-block align-text-bottom mr-2 text-brand-600 shrink-0 ' . $extraClass);
        return '<span class="' . $cls . '" aria-hidden="true">' . $svg . '</span>';
    }

    private static function svgFor(string $key): ?string
    {
        $map = [
            // Movement / travel
            'pin' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>',
            'map' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z"/></svg>',
            'car' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0H15M5.25 18.75h-1.5a1.5 1.5 0 0 1-1.5-1.5V14.25a1.5 1.5 0 0 1 1.5-1.5h16.5a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-1.5m-3 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0M5.25 12.75 7.5 6.75h9l2.25 6"/></svg>',
            'ferry' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5s1.5 1 3 1 3-1 3-1 1.5 1 3 1 3-1 3-1 1.5 1 3 1 3-1 3-1M4.5 14V8a1.5 1.5 0 0 1 1.5-1.5h12A1.5 1.5 0 0 1 19.5 8v6"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.5V4.5h6v2"/></svg>',
            'plane' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>',
            // Food / dining
            'food' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>',
            // Time / season
            'calendar' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>',
            'clock' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>',
            'sun' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/></svg>',
            // Money / cost
            'money' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-2.21 0-4-1.79-4-4 0-1.105 1.343-2 3-2 .953 0 1.831.29 2.5.749M12 6V4.5"/></svg>',
            // Sleep / stay
            'bed' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/></svg>',
            // Info / tips
            'info' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>',
            'tip' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18"/></svg>',
            // Camera / photo / view
            'camera' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"/></svg>',
            // Beach / wave
            'wave' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5c2 0 2-1.5 4-1.5s2 1.5 4 1.5 2-1.5 4-1.5 2 1.5 4 1.5 2-1.5 4-1.5M3 20c2 0 2-1.5 4-1.5s2 1.5 4 1.5 2-1.5 4-1.5 2 1.5 4 1.5 2-1.5 4-1.5M3 13c2 0 2-1.5 4-1.5s2 1.5 4 1.5 2-1.5 4-1.5 2 1.5 4 1.5 2-1.5 4-1.5"/></svg>',
            // Mountain / nature
            'mountain' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75 7.409 9.624a1.5 1.5 0 0 1 2.282 0L13.5 14.25M14.25 12l3.659-3.376a1.5 1.5 0 0 1 2.282 0L21.75 10.5M19.5 7.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 19.5h19.5"/></svg>',
            // List / checklist
            'check' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>',
            // Question mark / FAQ
            'question' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"/></svg>',
            // Building / heritage / city
            'building' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5M3.75 21V8.25a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 .75.75V21M15.75 21V3.75A.75.75 0 0 1 16.5 3h3a.75.75 0 0 1 .75.75V21M9.75 6.75h.008v.008H9.75V6.75ZM9.75 9.75h.008v.008H9.75V9.75ZM9.75 12.75h.008v.008H9.75v-.008ZM18 9.75h.008v.008H18V9.75ZM18 12.75h.008v.008H18v-.008Z"/></svg>',
            // People / group / family
            'people' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>',
            // Star / highlight
            'star' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.32.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .32-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>',
            // Warning / caution
            'warning' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>',
        ];
        return $map[$key] ?? null;
    }

    /**
     * Match a heading text to an icon key. Returns 'pin' as a generic fallback
     * so every H2 still gets a marker (rather than visual inconsistency).
     */
    public static function pickFromHeading(string $heading): string
    {
        $h = strtolower($heading);
        $patterns = [
            'food' => ['food', 'eat', 'dish', 'restaurant', 'kain', 'lunch', 'dinner', 'breakfast', 'meal', 'kakanin', 'cuisine', 'cafe', 'coffee'],
            'bed' => ['stay', 'sleep', 'hotel', 'resort', 'lodge', 'accommodation', 'inn', 'where to stay', 'overnight'],
            'car' => ['how to get', 'getting there', 'getting around', 'commute', 'route', 'transport', 'transit', 'bus', 'jeepney', 'tricycle', 'grab', 'drive'],
            'ferry' => ['ferry', 'boat', 'ferries', 'banca', 'pumpboat'],
            'plane' => ['airline', 'flight', 'airport', 'flights', 'plane'],
            'calendar' => ['when to go', 'season', 'best time', 'best month', 'best months', 'festival', 'event', 'schedule', 'calendar'],
            'clock' => ['itinerary', 'day 1', 'day 2', 'day-by-day', 'day by day', 'hours', 'morning', 'afternoon', 'evening', 'three-day', 'two-day', 'four-day', 'five-day', 'weekend', '24 hours', '48 hours', '72 hours'],
            'money' => ['cost', 'budget', 'price', 'fare', 'fee', 'expense', 'how much', 'cheap', 'affordable', 'sulit'],
            'tip' => ['tip', 'tips', 'advice', 'know before', 'note', 'reminder', 'mistake', 'first-timer', 'first timer'],
            'camera' => ['photo', 'spot', 'view', 'viewpoint', 'instagram', 'shoot', 'sunset', 'sunrise', 'scenic'],
            'wave' => ['beach', 'beaches', 'island hopping', 'snorkel', 'snorkeling', 'dive', 'diving', 'surf', 'lagoon', 'cove', 'reef', 'underwater'],
            'mountain' => ['hike', 'hiking', 'trek', 'trekking', 'climb', 'summit', 'peak', 'mountain', 'mt.', 'mt ', 'falls', 'waterfall', 'volcano', 'crater'],
            'building' => ['church', 'cathedral', 'heritage', 'museum', 'plaza', 'monument', 'ruins', 'ancestral', 'colonial', 'historic'],
            'people' => ['family', 'kids', 'children', 'group', 'barkada', 'friends', 'couple', 'solo'],
            'question' => ['faq', 'questions', 'frequently asked', 'q&a', 'q & a', 'common questions'],
            'star' => ['best', 'top', 'must', 'highlight', 'why'],
            'warning' => ['avoid', 'do not', "don't", 'mistake', 'scam', 'danger', 'caution', 'beware'],
            'map' => ['map', 'where', 'location', 'area', 'region', 'cluster'],
            'sun' => ['weather', 'climate', 'rain', 'wet', 'dry', 'sunny'],
            'check' => ['plan', 'planning', 'checklist', 'pack', 'packing', 'what to bring'],
        ];
        foreach ($patterns as $key => $needles) {
            foreach ($needles as $n) {
                if (strpos($h, $n) !== false) return $key;
            }
        }
        return 'pin';
    }
}
