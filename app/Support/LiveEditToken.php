<?php

namespace App\Support;

/**
 * HMAC-signed token used by the mother super-admin's Live Editor to
 * request a frontend keyword page in "live-edit" mode.
 *
 * The mother app generates a token tied to the slug + an expiry,
 * appends it to the iframe src as `?_lt={token}.{expiry}`, and the
 * frontend's KeywordPageController validates it before exposing edit
 * chrome. Token validity is scoped to the exact slug so a token for
 * "resort-in-cebu" cannot be replayed against "resort-in-bohol".
 *
 * Shared secret lives at `services.live_edit.secret` (env
 * LIVE_EDIT_SECRET). Both apps must use the same value.
 */
class LiveEditToken
{
    /**
     * Verifies a "{token}.{expiry}" pair for a given slug. Returns
     * true iff the signature matches and the expiry is in the future.
     */
    public static function valid(string $slug, ?string $compound): bool
    {
        if (!$compound || !str_contains($compound, '.')) return false;
        [$token, $expiry] = explode('.', $compound, 2);
        $expiry = (int) $expiry;
        if ($expiry < time()) return false;
        $secret = (string) config('services.live_edit.secret');
        if ($secret === '') return false;
        $expected = hash_hmac('sha256', $slug . '|' . $expiry, $secret);
        return hash_equals($expected, $token);
    }

    /**
     * Generates a fresh "{token}.{expiry}" pair for a slug. TTL is in
     * seconds; default is 1 hour which is long enough for an admin
     * session but short enough that leaked URLs expire quickly.
     */
    public static function make(string $slug, int $ttl = 3600): string
    {
        $expiry = time() + $ttl;
        $secret = (string) config('services.live_edit.secret');
        $token = hash_hmac('sha256', $slug . '|' . $expiry, $secret);
        return $token . '.' . $expiry;
    }
}
