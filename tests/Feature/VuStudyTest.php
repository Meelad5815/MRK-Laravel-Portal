<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VuStudyItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VuStudyTest extends TestCase
{
    use RefreshDatabase;

    public function test_vu_study_dashboard_requires_authentication(): void
    {
        $this->get(route('vu-study.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_save_a_manual_study_item(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('vu-study.store'), [
            'course_code' => 'CS101',
            'course_title' => 'Introduction to Computing',
            'item_type' => 'quiz',
            'title' => 'Quiz 3',
            'due_at' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ])->assertRedirect();

        $this->assertDatabaseHas('vu_study_items', [
            'user_id' => $user->id,
            'course_code' => 'CS101',
            'item_type' => 'quiz',
            'title' => 'Quiz 3',
            'status' => 'manual',
        ]);

        $this->actingAs($user)->get(route('vu-study.index'))
            ->assertOk()
            ->assertSee('Quiz 3')
            ->assertSee('CS101');
    }

    public function test_user_sees_only_their_own_study_items(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        VuStudyItem::create([
            'user_id' => $owner->id,
            'item_type' => 'assignment',
            'title' => 'My Assignment',
            'source_key' => 'owner-item',
        ]);
        VuStudyItem::create([
            'user_id' => $other->id,
            'item_type' => 'assignment',
            'title' => 'Private Other Assignment',
            'source_key' => 'other-item',
        ]);

        $this->actingAs($owner)->get(route('vu-study.index'))
            ->assertOk()
            ->assertSee('My Assignment')
            ->assertDontSee('Private Other Assignment');
    }

    public function test_study_items_can_be_filtered_by_type_and_search_term(): void
    {
        $user = User::factory()->create();

        VuStudyItem::create([
            'user_id' => $user->id,
            'course_code' => 'CS101',
            'item_type' => 'quiz',
            'title' => 'CS101 Quiz Three',
            'source_key' => 'cs101-quiz',
        ]);
        VuStudyItem::create([
            'user_id' => $user->id,
            'course_code' => 'MTH202',
            'item_type' => 'assignment',
            'title' => 'Statistics Assignment',
            'source_key' => 'mth-assignment',
        ]);

        $this->actingAs($user)->get(route('vu-study.index', ['type' => 'quiz', 'q' => 'CS101']))
            ->assertOk()
            ->assertSee('CS101 Quiz Three')
            ->assertDontSee('Statistics Assignment');
    }

    public function test_sync_requires_token_and_does_not_reset_a_completed_item(): void
    {
        $user = User::factory()->create();
        $token = str_repeat('s', 48);
        config(['services.vu_sync.token' => $token]);

        $existing = VuStudyItem::create([
            'user_id' => $user->id,
            'course_code' => 'CS101',
            'item_type' => 'quiz',
            'title' => 'Quiz Three',
            'status' => 'completed',
            'source_key' => 'vulms-cs101-quiz-three',
        ]);

        $payload = [
            'items' => [[
                'course_code' => 'CS101',
                'item_type' => 'quiz',
                'title' => 'Quiz Three Updated',
                'source_key' => 'vulms-cs101-quiz-three',
            ]],
        ];

        $this->actingAs($user)->postJson(route('vu-study.sync'), $payload)
            ->assertForbidden();

        $this->actingAs($user)->postJson(route('vu-study.sync'), $payload, [
            'X-MRK-Sync-Token' => $token,
        ])->assertOk()->assertJsonPath('received', 1);

        $this->assertDatabaseHas('vu_study_items', [
            'id' => $existing->id,
            'title' => 'Quiz Three Updated',
            'status' => 'completed',
        ]);
    }
}
