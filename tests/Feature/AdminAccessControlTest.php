<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Villa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessControlTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test unauthenticated users cannot access admin routes
     */
    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    /**
     * Test regular users cannot access admin routes
     */
    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertForbidden();
    }

    /**
     * Test admin users can access admin dashboard
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@vaninavilla.com',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
    }

    /**
     * Test regular user cannot access villa management
     */
    public function test_regular_user_cannot_access_villa_management(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/admin/villas');

        $response->assertForbidden();
    }

    /**
     * Test admin can access villa management
     */
    public function test_admin_can_access_villa_management(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@vaninavilla.com',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin/villas');

        $response->assertOk();
    }

    /**
     * Test User model isAdmin() helper method
     */
    public function test_is_admin_helper_returns_true_for_admin(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->assertTrue($admin->isAdmin());
    }

    /**
     * Test User model isAdmin() helper returns false for regular user
     */
    public function test_is_admin_helper_returns_false_for_regular_user(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->assertFalse($user->isAdmin());
    }

    /**
     * Test all admin routes are protected
     */
    public function test_all_admin_routes_require_authentication(): void
    {
        $adminRoutes = [
            '/admin',
            '/admin/villas',
            '/admin/villas/create',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    /**
     * Test regular user cannot create villas
     */
    public function test_regular_user_cannot_create_villa(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->post('/admin/villas', [
            'slug' => 'test-villa',
            'featured' => false,
        ]);

        $response->assertForbidden();
    }

    /**
     * Test regular user cannot update villas
     */
    public function test_regular_user_cannot_update_villa(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        // Create a villa first so it exists
        $villa = Villa::create([
            'slug' => 'test-villa',
            'featured' => false,
            'display_order' => 1,
        ]);

        $response = $this->actingAs($user)->put("/admin/villas/{$villa->id}", [
            'slug' => 'updated-villa',
        ]);

        $response->assertForbidden();
    }

    /**
     * Test regular user cannot delete villas
     */
    public function test_regular_user_cannot_delete_villa(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        // Create a villa first so it exists
        $villa = Villa::create([
            'slug' => 'test-villa',
            'featured' => false,
            'display_order' => 1,
        ]);

        $response = $this->actingAs($user)->delete("/admin/villas/{$villa->id}");

        $response->assertForbidden();
    }
}
