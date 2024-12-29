<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlexibleThrottleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_allows_requests_within_limit()
    {
        for ($i = 0; $i < 3; $i++) {
            $response = $this->get('/test-throttle');
            $response->assertStatus(200);
        }
    }

    /** @test */
    public function it_blocks_requests_exceeding_limit()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->get('/test-throttle');
        }

        $response = $this->get('/test-throttle');
        $response->assertStatus(429);
    }
}