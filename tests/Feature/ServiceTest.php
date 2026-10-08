<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_services_are_visible(): void
    {
        $service = Service::create([
            'title' => 'Test Service',
            'slug' => 'test-service',
            'summary' => 'A test service summary.',
            'description' => 'A test service description.',
            'category' => 'Testing',
            'status' => 'published',
            'featured' => true,
            'sort_order' => 1,
        ]);

        $this->get('/services')
            ->assertOk()
            ->assertSee($service->title);

        $this->get('/services/test-service')
            ->assertOk()
            ->assertSee($service->summary);
    }

    public function test_non_admin_cannot_manage_services(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/dashboard/services')
            ->assertForbidden();
    }

    public function test_admin_can_create_service(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post('/dashboard/services', [
            'title' => 'Managed Service',
            'summary' => 'Managed service summary.',
            'description' => 'Managed service description.',
            'category' => 'Testing',
            'status' => 'published',
            'sort_order' => 10,
        ])->assertRedirect('/dashboard/services');

        $this->assertDatabaseHas('services', [
            'title' => 'Managed Service',
            'slug' => 'managed-service',
            'status' => 'published',
        ]);
    }
}
