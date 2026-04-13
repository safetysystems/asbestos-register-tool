<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Home', [
            'tagline' => 'Inertia.js and Vue are now wired into this Laravel app.',
            'documentation' => 'https://inertiajs.com/',
            'starterChecklist' => [
                'Build pages in resources/js/Pages',
                'Return Inertia::render(...) from your controllers',
                'Run npm run dev while developing the frontend',
            ],
        ]);
    }
}
