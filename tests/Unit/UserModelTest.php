<?php

namespace Tests\Unit;

use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user has LogsActivity trait
     */
    public function test_user_has_logs_activity_trait(): void
    {
        $traits = class_uses(User::class);

        $this->assertContains('App\Traits\LogsActivity', $traits);
    }

    /**
     * Test isAdmin method returns true for admin user
     */
    public function test_is_admin_returns_true_for_admin(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->assertTrue($admin->isAdmin());
    }

    /**
     * Test isAdmin method returns false for regular user
     */
    public function test_is_admin_returns_false_for_user(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->assertFalse($user->isAdmin());
    }

    /**
     * Test user has activityLogs relationship
     */
    public function test_user_has_activity_logs_relationship(): void
    {
        $user = User::factory()->create();

        AdminActivityLog::create([
            'user_id' => $user->id,
            'action' => 'test action',
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->activityLogs);
        $this->assertCount(1, $user->activityLogs);
    }

    /**
     * Test password is properly hashed
     */
    public function test_password_is_hashed(): void
    {
        $user = User::factory()->create([
            'password' => 'PlainPassword123!',
        ]);

        $this->assertNotEquals('PlainPassword123!', $user->password);
        $this->assertTrue(\Hash::check('PlainPassword123!', $user->password));
    }

    /**
     * Test password is hidden in array conversion
     */
    public function test_password_is_hidden_in_array(): void
    {
        $user = User::factory()->create();

        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    /**
     * Test email_verified_at is cast to datetime
     */
    public function test_email_verified_at_is_datetime(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
    }
}
