<?php
// Shared helpers for database/data/subject_photos.json (read by App\Support\GuideHtml).
use App\Support\GuideHtml;

function subject_registry_load(): array
{
    $f = database_path('data/subject_photos.json');
    return is_file($f) ? (json_decode((string) file_get_contents($f), true) ?: []) : [];
}

function subject_registry_save(array $reg): void
{
    foreach ($reg as $k => &$v) if (is_array($v)) ksort($v);
    unset($v);
    ksort($reg);
    file_put_contents(database_path('data/subject_photos.json'), json_encode($reg, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
}

/** Registers a checked photo for a subject; returns false when the subject already has one. */
function subject_registry_put(array &$reg, string $kind, string $name, string $place, string $src): bool
{
    $k = GuideHtml::nameKey($name);
    if ($k === '') return false;
    $key = $k . '|' . ($kind === 'food' ? '' : GuideHtml::nameKey($place));
    if (isset($reg[$kind][$key])) return false;
    $reg[$kind][$key] = $src;
    $reg[$kind][$k . '|'] ??= $src;
    return true;
}
