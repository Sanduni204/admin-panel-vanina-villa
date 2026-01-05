<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test X-Frame-Options header is set
     */
    public function test_x_frame_options_header_is_set(): void
    {
        $response = $this->get('/login');

        $response->assertHeader('X-Frame-Options', 'DENY');
    }

    /**
     * Test X-Content-Type-Options header is set
     */
    public function test_x_content_type_options_header_is_set(): void
    {
        $response = $this->get('/login');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    /**
     * Test X-XSS-Protection header is set
     */
    public function test_x_xss_protection_header_is_set(): void
    {
        $response = $this->get('/login');

        $response->assertHeader('X-XSS-Protection', '1; mode=block');
    }

    /**
     * Test Referrer-Policy header is set
     */
    public function test_referrer_policy_header_is_set(): void
    {
        $response = $this->get('/login');

        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * Test Content-Security-Policy header is set
     */
    public function test_content_security_policy_header_is_set(): void
    {
        $response = $this->get('/login');

        $csp = $response->headers->get('Content-Security-Policy');

        $this->assertNotNull($csp, 'Content-Security-Policy header should be present');
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString('https://cdn.jsdelivr.net', $csp);
    }

    /**
     * Test security headers are present on all routes
     */
    public function test_security_headers_on_public_routes(): void
    {
        $response = $this->get('/villas');

        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
    }

    /**
     * Test security headers are present on authenticated routes
     */
    public function test_security_headers_on_authenticated_routes(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/dashboard');

        // Should have security headers on authenticated routes
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }
}
