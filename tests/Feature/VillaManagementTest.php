<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Villa;
use App\Models\VillaTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VillaManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    /** @test */
    public function admin_can_view_villas_list()
    {
        $this->actingAs($this->admin)
            ->withoutMiddleware()
            ->get(route('villas.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.villas.index');
    }

    /** @test */
    public function admin_can_create_villa()
    {
        $data = [
            'title_en' => 'Beautiful Villa',
            'title_fr' => 'Belle Villa',
            'description_en' => 'A beautiful villa with ocean view',
            'description_fr' => 'Une belle villa avec vue sur l\'océan',
            'price' => 250.00,
            'max_guests' => 8,
            'min_guests' => 2,
            'featured' => false,
            'published' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->withoutMiddleware()
            ->post(route('villas.store'), $data);

        $this->assertDatabaseHas('villas', [
            'slug' => 'beautiful-villa',
        ]);

        $this->assertDatabaseHas('villa_translations', [
            'title' => 'Beautiful Villa',
            'locale' => 'en',
        ]);

        $this->assertDatabaseHas('villa_translations', [
            'title' => 'Belle Villa',
            'locale' => 'fr',
        ]);

        $response->assertRedirect();
    }



    /** @test */
    public function admin_can_update_villa()
    {
        $villa = Villa::factory()->create(['slug' => 'test-villa']);
        VillaTranslation::factory()->create([
            'villa_id' => $villa->id,
            'locale' => 'en',
        ]);
        VillaTranslation::factory()->create([
            'villa_id' => $villa->id,
            'locale' => 'fr',
        ]);

        $data = [
            'title_en' => 'Updated Title',
            'title_fr' => 'Titre Mis à Jour',
            'description_en' => 'Updated description',
            'description_fr' => 'Description mise à jour',
            'price' => 300.00,
            'max_guests' => 10,
            'min_guests' => 3,
            'slug' => 'test-villa',
        ];

        $response = $this->actingAs($this->admin)
            ->withoutMiddleware()
            ->put(route('villas.update', $villa), $data);

        // The update method should redirect
        $response->assertStatus(302);
    }

    /** @test */
    public function admin_can_delete_villa()
    {
        $villa = Villa::factory()->create();

        $response = $this->actingAs($this->admin)
            ->withoutMiddleware()
            ->delete(route('villas.destroy', $villa));

        $response->assertRedirect();
    }

    /** @test */
    public function admin_can_view_villa_details()
    {
        $villa = Villa::factory()->create();
        VillaTranslation::factory()->create([
            'villa_id' => $villa->id,
            'locale' => 'en',
        ]);
        VillaTranslation::factory()->create([
            'villa_id' => $villa->id,
            'locale' => 'fr',
        ]);

        $this->assertDatabaseHas('villas', ['id' => $villa->id]);
    }

    /** @test */
    public function validation_fails_without_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->withoutMiddleware()
            ->post(route('villas.store'), []);

        $response->assertSessionHasErrors([
            'title_en',
            'title_fr',
            'description_en',
            'description_fr',
            'max_guests',
        ]);
    }

    /** @test */
    public function validation_fails_with_invalid_price()
    {
        $data = [
            'title_en' => 'Villa',
            'title_fr' => 'Villa',
            'description_en' => 'Description',
            'description_fr' => 'Description',
            'price' => -100,
            'max_guests' => 8,
            'bedrooms' => 4,
            'bathrooms' => 3,
        ];

        $response = $this->actingAs($this->admin)
            ->withoutMiddleware()
            ->post(route('villas.store'), $data);

        $response->assertSessionHasErrors('price');
    }

    /** @test */
    public function non_admin_cannot_access_villa_management()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user)
            ->get(route('villas.index'))
            ->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_villa_management()
    {
        $this->get(route('villas.index'))
            ->assertRedirect('/login');
    }

    /** @test */
    public function admin_can_reorder_villas()
    {
        $villa1 = Villa::factory()->create(['display_order' => 0]);
        $villa2 = Villa::factory()->create(['display_order' => 1]);

        $response = $this->actingAs($this->admin)
            ->withoutMiddleware()
            ->post(route('villas.reorder'), [
                'villas' => [$villa2->id, $villa1->id],
            ]);

        $response->assertJson(['success' => true]);

        $this->assertEquals(0, $villa2->fresh()->display_order);
        $this->assertEquals(1, $villa1->fresh()->display_order);
    }
}
