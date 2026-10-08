<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_customer(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.customers.store'), [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '03001234567',
            'company' => 'Example Co',
            'status' => 'active',
            'priority' => 'high',
        ]);

        $response->assertRedirect(route('admin.customers.index'));
        $this->assertDatabaseHas('customers', ['email' => 'customer@example.com', 'priority' => 'high']);
    }

    public function test_non_admin_cannot_manage_customers(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get(route('admin.customers.index'))->assertForbidden();
    }

    public function test_customer_delete_redirects_to_customer_index(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = Customer::create(['name' => 'Delete Me', 'status' => 'active', 'priority' => 'normal']);

        $this->actingAs($admin)->delete(route('admin.customers.destroy', $customer))
            ->assertRedirect(route('admin.customers.index'));
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }
}