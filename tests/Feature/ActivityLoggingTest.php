<?php

namespace Tests\Feature;

use App\Models\AdminActivityLog;
use App\Models\User;
use App\Models\Villa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLoggingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin actions are logged
     */
    public function test_admin_post_request_is_logged(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@vaninavilla.com',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)->post('/admin/villas', [
            'slug' => 'test-villa',
            'featured' => false,
            'display_order' => 1,
        ]);

        $this->assertDatabaseHas('admin_activity_logs', [
            'user_id' => $admin->id,
            'action' => 'villas.store',
        ]);
    }

    /**
     * Test GET requests are not logged
     */
    public function test_admin_get_request_is_not_logged(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@vaninavilla.com',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)->get('/admin/villas');

        $this->assertDatabaseCount('admin_activity_logs', 0);
    }

    /**
     * Test model creation is logged via LogsActivity trait
     */
    public function test_villa_creation_is_logged(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@vaninavilla.com',
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $villa = Villa::create([
            'slug' => 'test-villa',
            'featured' => false,
            'display_order' => 1,
        ]);

        $this->assertDatabaseHas('admin_activity_logs', [
            'user_id' => $admin->id,
            'action' => 'created Villa',
            'entity_type' => 'Villa',
            'entity_id' => $villa->id,
        ]);
    }

    /**
     * Test model update is logged with old and new values
     */
    public function test_villa_update_is_logged_with_values(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@vaninavilla.com',
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $villa = Villa::create([
            'slug' => 'test-villa',
            'featured' => false,
            'display_order' => 1,
        ]);

        // Clear the creation log
        AdminActivityLog::where('action', 'created Villa')->delete();

        // Update the villa
        $villa->update([
            'featured' => true,
            'display_order' => 2,
        ]);

        $log = AdminActivityLog::where('action', 'updated Villa')->first();

        $this->assertNotNull($log);
        $this->assertEquals($admin->id, $log->user_id);
        $this->assertEquals('Villa', $log->entity_type);
        $this->assertEquals($villa->id, $log->entity_id);

        // Old and new values are stored in separate columns
        $this->assertNotNull($log->old_values);
        $this->assertNotNull($log->new_values);
        $this->assertIsArray($log->old_values);
        $this->assertIsArray($log->new_values);

        // Verify the values changed
        $this->assertEquals(false, $log->old_values['featured']);
        $this->assertEquals(true, $log->new_values['featured']);
    }

    /**
     * Test model deletion is logged
     */
    public function test_villa_deletion_is_logged(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@vaninavilla.com',
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $villa = Villa::create([
            'slug' => 'test-villa',
            'featured' => false,
            'display_order' => 1,
        ]);

        $villaId = $villa->id;

        // Clear previous logs
        AdminActivityLog::whereIn('action', ['created Villa'])->delete();

        $villa->delete();

        $this->assertDatabaseHas('admin_activity_logs', [
            'user_id' => $admin->id,
            'action' => 'deleted Villa',
            'entity_type' => 'Villa',
            'entity_id' => $villaId,
        ]);
    }

    /**
     * Test sensitive data is filtered from logs
     */
    public function test_passwords_are_filtered_from_activity_logs(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@vaninavilla.com',
            'role' => 'admin',
        ]);

        // Simulate a POST request with password
        $this->actingAs($admin)->post('/admin/villas', [
            'slug' => 'test-villa',
            'password' => 'SecretPass123!',
            'password_confirmation' => 'SecretPass123!',
            '_token' => 'fake-token',
        ]);

        $log = AdminActivityLog::where('user_id', $admin->id)->first();

        $this->assertNotNull($log);

        $payload = $log->new_values['new'] ?? [];
        $this->assertArrayNotHasKey('password', $payload);
        $this->assertArrayNotHasKey('password_confirmation', $payload);
        $this->assertArrayNotHasKey('_token', $payload);
    }

    /**
     * Test IP address is recorded
     */
    public function test_ip_address_is_recorded_in_activity_log(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@vaninavilla.com',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->withServerVariables(['REMOTE_ADDR' => '192.168.1.100'])
            ->post('/admin/villas', [
                'slug' => 'test-villa',
            ]);

        $log = AdminActivityLog::where('user_id', $admin->id)->first();

        $this->assertNotNull($log);
        $this->assertEquals('192.168.1.100', $log->ip_address);
    }

    /**
     * Test user agent is recorded
     */
    public function test_user_agent_is_recorded_in_activity_log(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@vaninavilla.com',
            'role' => 'admin',
        ]);

        $userAgent = 'Mozilla/5.0 Test Browser';

        $this->actingAs($admin)
            ->withHeaders(['User-Agent' => $userAgent])
            ->post('/admin/villas', [
                'slug' => 'test-villa',
            ]);

        $log = AdminActivityLog::where('user_id', $admin->id)->first();

        $this->assertNotNull($log);
        $this->assertEquals($userAgent, $log->user_agent);
    }

    /**
     * Test activity logs relationship with user
     */
    public function test_activity_log_belongs_to_user(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@vaninavilla.com',
            'role' => 'admin',
        ]);

        $log = AdminActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'test action',
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertInstanceOf(User::class, $log->user);
        $this->assertEquals($admin->id, $log->user->id);
    }

    /**
     * Test user has many activity logs
     */
    public function test_user_has_many_activity_logs(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@vaninavilla.com',
            'role' => 'admin',
        ]);

        AdminActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'action 1',
            'ip_address' => '127.0.0.1',
        ]);

        AdminActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'action 2',
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertCount(2, $admin->activityLogs);
    }

    /**
     * Test actions without authentication are not logged
     */
    public function test_unauthenticated_actions_are_not_logged(): void
    {
        $villa = Villa::create([
            'slug' => 'test-villa',
            'featured' => false,
            'display_order' => 1,
        ]);

        $this->assertDatabaseCount('admin_activity_logs', 0);
    }
}
