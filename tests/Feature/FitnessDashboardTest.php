<?php

namespace Tests\\Feature;

use App\\Models\\User;
use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use Illuminate\\Support\\Facades\\DB;
use Tests\\TestCase;

class FitnessDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_fitness_dashboard_requires_authentication(): void
    {
        $this->get(route('fitness.index'))->assertRedirect(route('login'));
    }

    public function test_user_sees_only_their_own_fitness_entries(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        DB::table('fitness_entries')->insert([
            [
                'user_id' => $owner->id,
                'type' => 'workout',
                'entry_date' => now()->toDateString(),
                'title' => 'Owner workout',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $other->id,
                'type' => 'workout',
                'entry_date' => now()->toDateString(),
                'title' => 'Private other workout',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->actingAs($owner)
            ->get(route('fitness.index'))
            ->assertOk()
            ->assertSee('Owner workout')
            ->assertDontSee('Private other workout');
    }

    public function test_authenticated_user_can_save_a_workout_entry(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('fitness.store'), [
            'type' => 'workout',
            'entry_date' => now()->toDateString(),
            'title' => 'Morning walk and exercise',
            'duration_minutes' => 30,
            'notes' => 'Test entry',
        ])->assertRedirect(route('fitness.index'));

        $this->assertDatabaseHas('fitness_entries', [
            'user_id' => $user->id,
            'type' => 'workout',
            'title' => 'Morning walk and exercise',
            'duration_minutes' => 30,
        ]);
    }

    public function test_backup_feed_requires_the_configured_bearer_token(): void
    {
        $user = User::factory()->create();
        config([
            'services.fitness_sheets.token' => str_repeat('a', 40),
            'services.fitness_sheets.user_id' => $user->id,
        ]);

        $this->getJson(route('fitness.backup.feed'))->assertUnauthorized();

        $this->withToken(str_repeat('a', 40))
            ->getJson(route('fitness.backup.feed'))
            ->assertOk()
            ->assertJsonPath('schema_version', 1)
            ->assertJsonPath('count', 0);
    }
}
