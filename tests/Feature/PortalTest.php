<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_available(): void
    {
        $this->get('/')->assertOk()->assertSee('MRK Digital');
    }

    public function test_registration_makes_the_first_user_an_admin(): void
    {
        $response = $this->post('/register', [
            'name' => 'MRK Admin',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertTrue(User::first()->is_admin);
    }

    public function test_contact_form_creates_a_new_lead(): void
    {
        $this->post('/contact', [
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'phone' => '03001234567',
            'project_type' => 'Website',
            'message' => 'I need a business website.',
        ])->assertRedirect();

        $this->assertDatabaseHas('leads', [
            'email' => 'client@example.com',
            'status' => 'new',
        ]);
    }

    public function test_non_admin_cannot_access_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get('/dashboard')->assertForbidden();
    }

    public function test_admin_can_update_lead_status(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $lead = Lead::create([
            'name' => 'Client',
            'email' => 'client@example.com',
            'project_type' => 'Web App',
            'message' => 'Build a portal.',
            'status' => 'new',
        ]);

        $this->actingAs($admin)
            ->patch('/dashboard/leads/'.$lead->id, ['status' => 'in_progress'])
            ->assertRedirect('/dashboard');

        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'status' => 'in_progress']);
    }
}
