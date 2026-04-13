<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
                ->component('Home')
                ->where('tagline', 'Inertia.js and Vue are now wired into this Laravel app.')
                ->where('appName', config('app.name'))
                ->has('starterChecklist', 3)
            );
    }
}
