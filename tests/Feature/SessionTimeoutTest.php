<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class SessionTimeoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test session lifetime is set to 60 minutes in production config
     */
    public function test_session_lifetime_is_sixty_minutes(): void
    {
        // Read directly from config file since phpunit.xml overrides runtime config
        $configFile = config_path('session.php');
        $configContent = file_get_contents($configFile);

        // Check that default value is 60
        $this->assertStringContainsString("env('SESSION_LIFETIME', 60)", $configContent);
    }

    /**
     * Test session database driver is configured for production
     */
    public function test_session_driver_is_database(): void
    {
        // Read directly from config file since phpunit.xml overrides to 'array' for testing
        $configFile = config_path('session.php');
        $configContent = file_get_contents($configFile);

        // Check that default driver is database
        $this->assertStringContainsString("env('SESSION_DRIVER', 'database')", $configContent);
    }

    /**
     * Test last activity timestamp is set on authenticated request
     */
    public function test_last_activity_timestamp_is_set(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard');

        $lastActivity = Session::get('last_activity_timestamp');

        $this->assertNotNull($lastActivity);
        $this->assertIsInt($lastActivity);
    }

    /**
     * Test last activity timestamp is updated on subsequent requests
     */
    public function test_last_activity_timestamp_is_updated(): void
    {
        $user = User::factory()->create();

        // First request
        $this->actingAs($user)->get('/dashboard');
        $firstTimestamp = Session::get('last_activity_timestamp');

        // Wait a moment
        sleep(1);

        // Second request
        $this->actingAs($user)->get('/dashboard');
        $secondTimestamp = Session::get('last_activity_timestamp');

        $this->assertGreaterThan($firstTimestamp, $secondTimestamp);
    }

    /**
     * Test session timeout middleware only checks authenticated users
     */
    public function test_session_timeout_does_not_affect_guests(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $this->assertNull(Session::get('last_activity_timestamp'));
    }

    /**
     * Test active sessions maintain authentication
     */
    public function test_active_session_maintains_authentication(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // Simulate activity within timeout period
        for ($i = 0; $i < 3; $i++) {
            $response = $this->get('/dashboard');
            $response->assertOk();
            $this->assertAuthenticated();
        }
    }
}
