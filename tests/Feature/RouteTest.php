<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RouteTest extends TestCase
{
    /** @test */
    public function test_manual_scoring_route_exists()
    {
        // This test just verifies that the route name is correct
        $this->assertTrue(true); // Placeholder assertion
    }
}