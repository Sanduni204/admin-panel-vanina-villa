<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Villa;
use App\Models\AdminActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogsActivityTraitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test trait logs model creation
     */
    public function test_trait_logs_model_creation(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@vaninavilla.com',
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        $villa = Villa::create([
            'slug' => 'test-villa',
            'featured' => false,
            'display_order' => 1,
        ]);

        $this->assertDatabaseHas('admin_activity_logs', [
            'user_id' => $user->id,
            'action' => 'created Villa',
            'entity_type' => 'Villa',
            'entity_id' => $villa->id,
        ]);
    }

    /**
     * Test trait logs model updates
     */
    public function test_trait_logs_model_updates(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@vaninavilla.com',
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        $villa = Villa::create([
            'slug' => 'test-villa',
            'featured' => false,
            'display_order' => 1,
        ]);

        // Clear creation log
        AdminActivityLog::where('action', 'created Villa')->delete();

        $villa->update(['featured' => true]);

        $log = AdminActivityLog::where('action', 'updated Villa')->first();

        $this->assertNotNull($log);
        $this->assertEquals('Villa', $log->entity_type);
        $this->assertEquals($villa->id, $log->entity_id);
    }

    /**
     * Test trait logs model deletion
     */
    public function test_trait_logs_model_deletion(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@vaninavilla.com',
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        $villa = Villa::create([
            'slug' => 'test-villa',
            'featured' => false,
            'display_order' => 1,
        ]);

        $villaId = $villa->id;

        // Clear creation log
        AdminActivityLog::where('action', 'created Villa')->delete();

        $villa->delete();

        $this->assertDatabaseHas('admin_activity_logs', [
            'user_id' => $user->id,
            'action' => 'deleted Villa',
            'entity_type' => 'Villa',
            'entity_id' => $villaId,
        ]);
    }

    /**
     * Test trait only logs when user is authenticated
     */
    public function test_trait_only_logs_when_authenticated(): void
    {
        // Not authenticated
        $villa = Villa::create([
            'slug' => 'test-villa',
            'featured' => false,
            'display_order' => 1,
        ]);

        $this->assertDatabaseCount('admin_activity_logs', 0);
    }

    /**
     * Test trait captures old and new values on update
     */
    public function test_trait_captures_old_and_new_values(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@vaninavilla.com',
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        $villa = Villa::create([
            'slug' => 'original-slug',
            'featured' => false,
            'display_order' => 1,
        ]);

        AdminActivityLog::truncate();

        $villa->update([
            'slug' => 'updated-slug',
            'featured' => true,
        ]);

        $log = AdminActivityLog::first();

        // Old and new values are stored in separate columns
        $this->assertNotNull($log->old_values);
        $this->assertNotNull($log->new_values);
        $this->assertIsArray($log->old_values);
        $this->assertIsArray($log->new_values);

        $this->assertEquals('original-slug', $log->old_values['slug']);
        $this->assertEquals('updated-slug', $log->new_values['slug']);
        $this->assertEquals(false, $log->old_values['featured']);
        $this->assertEquals(true, $log->new_values['featured']);
    }
}
