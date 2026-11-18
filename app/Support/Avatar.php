<?php

namespace App\Support;

/**
 * Locally generated initials avatar (inline SVG data URI). Used as the
 * fallback when an author, reviewer or commenter has no photo, so pages
 * never depend on a third-party avatar API.
 */
class Avatar
{
    private const PALETTE = [
        ['#d6eaf5', '#11527f'], ['#fdf3e2', '#9a4b09'], ['#e7f6ef', '#047857'],
        ['#fbecea', '#a93226'], ['#eeeffd', '#4338ca'], ['#f3eefe', '#6d28d9'],
        ['#e5f5f3', '#0f766e'], ['#fff3ef', '#b33421'],
    ];

    public static function initials(?string $name): string
    {
        $name = trim((string) $name);
        $parts = preg_split('/\s+/u', preg_replace('/["\'“”()]/u', '', $name)) ?: [];
        $letters = '';
        foreach ([reset($parts), count($parts) > 1 ? end($parts) : ''] as $p) {
            if ($p !== '' && $p !== false) $letters .= mb_strtoupper(mb_substr($p, 0, 1));
        }
        $letters = $letters !== '' ? $letters : '?';
        [$bg, $fg] = self::PALETTE[abs(crc32($name)) % count(self::PALETTE)];

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96">'
            . '<rect width="96" height="96" rx="48" fill="' . $bg . '"/>'
            . '<text x="48" y="50" dy=".35em" text-anchor="middle" font-family="Plus Jakarta Sans,Inter,Arial,sans-serif" font-size="36" font-weight="700" fill="' . $fg . '">'
            . htmlspecialchars($letters, ENT_XML1) . '</text></svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
