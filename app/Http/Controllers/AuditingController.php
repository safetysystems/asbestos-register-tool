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
                            return [
                                'id' => $media->id,
                                'name' => $media->name,
                                'url' => $media->getUrl(),
                            ];
                        })->toArray(),
                    ];
                })->toArray(),
            ],
        ]);
    }
}
