<?php

namespace Tests\Feature;

use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLoggingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test GET requests are not logged
     */
    public function test_admin_get_request_is_not_logged(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@vaninavilla.com',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)->get('/admin');

        $this->assertDatabaseCount('admin_activity_logs', 0);
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
}
