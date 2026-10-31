<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDatabaseMaintenanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_run_database_migrations(): void
    {
        $this->post('/dashboard/system/run-pending-migrations', [
            'backup_confirmed' => '1',
        ])->assertRedirect('/login');
    }

    public function test_non_admin_cannot_run_database_migrations(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->post('/dashboard/system/run-pending-migrations', [
            'backup_confirmed' => '1',
        ])->assertForbidden();
    }

    public function test_admin_must_confirm_database_backup_before_migrations(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->from('/dashboard')->post('/dashboard/system/run-pending-migrations')
            ->assertRedirect('/dashboard')
            ->assertSessionHasErrors('backup_confirmed');
    }

    public function test_admin_can_run_pending_migrations_after_confirming_backup(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post('/dashboard/system/run-pending-migrations', [
            'backup_confirmed' => '1',
        ])->assertRedirect('/dashboard')
            ->assertSessionHas('success', 'Pending database updates completed.');
    }
}
