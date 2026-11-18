<?php

namespace App\Http\Middleware;

use App\Support\ContentSnapshot;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Safety net for content written with absolute dev-machine URLs
 * (http://resortguruph.test/...). Those resolve locally but are broken on
 * the live site, so HTML/JSON/XML responses get them rewritten to
 * root-relative paths. The snapshot and cloud sync already clean the
 * data; this catches anything added later through the admin.
 */
class RewriteLocalUrls
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $type = (string) $response->headers->get('Content-Type', '');
        if ($response->isSuccessful() && !$response->headers->has('Content-Disposition')
            && (str_contains($type, 'html') || str_contains($type, 'json') || str_contains($type, 'xml'))
            && method_exists($response, 'getContent')) {
            $content = $response->getContent();
            if (is_string($content) && $content !== '') {
                $rewritten = ContentSnapshot::rewriteLocalUrls($content);
                // sitemap.xml / JSON-LD need absolute URLs: point them at this host.
                if ($rewritten !== $content && (str_contains($type, 'xml'))) {
                    $rewritten = str_replace(['<loc>/', '<image:loc>/'], ['<loc>' . $request->getSchemeAndHttpHost() . '/', '<image:loc>' . $request->getSchemeAndHttpHost() . '/'], $rewritten);
                }
                if ($rewritten !== $content) {
                    $response->setContent($rewritten);
                }
            }
        }

        return $response;
    }
}
