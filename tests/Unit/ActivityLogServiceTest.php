<?php

namespace Tests\Unit;

use App\Models\AdminActivityLog;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ActivityLogServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test log method creates activity log entry
     */
    public function test_log_creates_activity_log_entry(): void
    {
        $user = User::factory()->create();
        $service = new ActivityLogService();

        $service->log(
            $user,
            'test action',
            ['key' => 'value'],
            'TestEntity',
            123
        );

        $this->assertDatabaseHas('admin_activity_logs', [
            'user_id' => $user->id,
            'action' => 'test action',
            'entity_type' => 'TestEntity',
            'entity_id' => 123,
        ]);
    }

    /**
     * Test log stores context as new_values
     */
    public function test_log_stores_context_as_new_values(): void
    {
        $user = User::factory()->create();
        $service = new ActivityLogService();

        $context = ['field' => 'value', 'number' => 42];

        $service->log($user, 'test action', $context);

        $log = AdminActivityLog::where('user_id', $user->id)->first();

        $this->assertEquals($context, $log->new_values);
    }

    /**
     * Test log stores old and new values separately
     */
    public function test_log_stores_old_and_new_values_separately(): void
    {
        $user = User::factory()->create();
        $service = new ActivityLogService();

        $context = [
            'old' => ['field' => 'old_value'],
            'new' => ['field' => 'new_value'],
        ];

        $service->log($user, 'update action', $context);

        $log = AdminActivityLog::where('user_id', $user->id)->first();

        $this->assertEquals(['field' => 'old_value'], $log->old_values);
        $this->assertEquals(['field' => 'new_value'], $log->new_values);
    }

    /**
     * Test log captures IP address from request
     */
    public function test_log_captures_ip_address_from_request(): void
    {
        $user = User::factory()->create();
        $service = new ActivityLogService();

        $request = Request::create('/test', 'POST');
        $request->server->set('REMOTE_ADDR', '192.168.1.1');

        $service->log($user, 'test action', [], null, null, $request);

        $log = AdminActivityLog::where('user_id', $user->id)->first();

        $this->assertEquals('192.168.1.1', $log->ip_address);
    }

    /**
     * Test log captures user agent from request
     */
    public function test_log_captures_user_agent_from_request(): void
    {
        $user = User::factory()->create();
        $service = new ActivityLogService();

        $userAgent = 'Mozilla/5.0 Test Browser';
        $request = Request::create('/test', 'POST', [], [], [], ['HTTP_USER_AGENT' => $userAgent]);

        $service->log($user, 'test action', [], null, null, $request);

        $log = AdminActivityLog::where('user_id', $user->id)->first();

        $this->assertEquals($userAgent, $log->user_agent);
    }

    /**
     * Test log works without request parameter
     */
    public function test_log_works_without_request_parameter(): void
    {
        $user = User::factory()->create();
        $service = new ActivityLogService();

        $service->log($user, 'test action', ['data' => 'value']);

        $this->assertDatabaseHas('admin_activity_logs', [
            'user_id' => $user->id,
            'action' => 'test action',
        ]);
    }

    /**
     * Test log accepts nullable entity type and id
     */
    public function test_log_accepts_nullable_entity_type_and_id(): void
    {
        $user = User::factory()->create();
        $service = new ActivityLogService();

        $service->log($user, 'general action', ['info' => 'data'], null, null);

        $log = AdminActivityLog::where('user_id', $user->id)->first();

        $this->assertNull($log->entity_type);
        $this->assertNull($log->entity_id);
    }
}
