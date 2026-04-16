<?php

namespace App\Http\Controllers;

use App\Models\PropertyAsbestosAudit;
use Inertia\Inertia;
use Inertia\Response;

class AuditingController extends Controller
{
    public function show(PropertyAsbestosAudit $asbestosaudit): Response
    {
        $asbestosaudit->load(['property.customer', 'samples.media']);

        return Inertia::render('Audit/Index', [
            'audit' => [
                ...$asbestosaudit->toArray(),
                'samples' => $asbestosaudit->samples->map(function ($sample): array {
                    return [
                        ...$sample->toArray(),
                        'media' => $sample->media->map(function ($media): array {
                            $url = $media->getUrl();
                            // Avoid mixed-content issues (http vs https) by returning a same-host relative URL when possible.
                            $parts = parse_url($url);
                            $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
                            $requestHost = request()->getHost();
                            $relativeUrl = null;

                            if (is_array($parts) && ! empty($parts['host']) && ! empty($parts['path'])) {
                                if ($parts['host'] === $requestHost || ($appHost && $parts['host'] === $appHost)) {
                                    $relativeUrl = $parts['path'];

                                    if (! empty($parts['query'])) {
                                        $relativeUrl .= '?'.$parts['query'];
                                    }
                                }
                            }

                            return [
                                'id' => $media->id,
                                'name' => $media->name,
                                'url' => $relativeUrl ?: $url,
                            ];
                        })->toArray(),
                    ];
                })->toArray(),
            ],
        ]);
    }
}
