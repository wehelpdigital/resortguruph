{{--
    TL;DR + WWWW summary cards for blog posts. Same markup as the
    tldr_card / wwww_card content blocks (BlockRenderer::summaryCards),
    so posts and guides look the same.

    Props:
      $tldr  → string|null. Plain paragraph OR `* bullet\n* bullet` lines.
      $wwww  → array|null with keys: why, when, where, whom.
--}}
{!! app(\App\Services\BlockRenderer::class)->summaryCards($tldr ?? null, $wwww ?? null) !!}
