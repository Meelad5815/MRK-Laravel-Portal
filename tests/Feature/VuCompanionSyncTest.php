<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VuCompanionToken;
use App\Models\VuStudyItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VuCompanionSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_companion_token_is_shown_once_and_only_hash_is_stored(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('vu-study.companion-token.create'));

        $response->assertRedirect();
        $plainToken = session('companion_token_once');
        $this->assertIsString($plainToken);
        $this->assertSame(64, strlen($plainToken));

        $this->assertDatabaseHas('vu_companion_tokens', [
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $plainToken),
            'revoked_at' => null,
        ]);
        $this->assertDatabaseMissing('vu_companion_tokens', [
            'user_id' => $user->id,
            'token_hash' => $plainToken,
        ]);
    }

    public function test_companion_sync_requires_a_valid_personal_bearer_token(): void
    {
        $this->postJson('/api/vu-study/sync', [
            'items' => [[
                'item_type' => 'quiz',
                'title' => 'CS101 Quiz 3',
                'source_key' => 'android-test-quiz-3',
            ]],
        ])->assertUnauthorized();

        $this->postJson('/api/vu-study/sync', ['items' => []], [
            'Authorization' => 'Bearer ' . str_repeat('x', 64),
        ])->assertUnauthorized();
    }

    public function test_companion_sync_saves_item_to_token_owner_and_preserves_status(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $plainToken = bin2hex(random_bytes(32));

        $token = VuCompanionToken::create([
            'user_id' => $owner->id,
            'token_hash' => hash('sha256', $plainToken),
            'label' => 'Android test',
            'expires_at' => now()->addMonth(),
        ]);

        $existing = VuStudyItem::create([
            'user_id' => $owner->id,
            'course_code' => 'CS101',
            'item_type' => 'quiz',
            'title' => 'Quiz 3',
            'status' => 'completed',
            'source_key' => 'android-existing-quiz-3',
        ]);

        $this->postJson('/api/vu-study/sync', [
            'items' => [[
                'course_code' => 'CS101',
                'course_title' => 'Introduction to Computing',
                'item_type' => 'quiz',
                'title' => 'Quiz 3 Updated',
                'due_at' => now()->addDays(5)->format('Y-m-d H:i:s'),
                'source_key' => 'android-existing-quiz-3',
                'metadata' => ['capture_method' => 'student_reviewed_webview_form'],
            ]],
        ], [
            'Authorization' => 'Bearer ' . $plainToken,
        ])->assertOk()->assertJsonPath('received', 1);

        $this->assertDatabaseHas('vu_study_items', [
            'id' => $existing->id,
            'user_id' => $owner->id,
            'title' => 'Quiz 3 Updated',
            'status' => 'completed',
        ]);
        $this->assertDatabaseMissing('vu_study_items', [
            'user_id' => $other->id,
            'source_key' => 'android-existing-quiz-3',
        ]);

        $this->assertNotNull($token->fresh()->last_used_at);
    }

    public function test_revoked_or_expired_companion_token_cannot_sync(): void
    {
        $user = User::factory()->create();
        $plainToken = bin2hex(random_bytes(32));

        VuCompanionToken::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $plainToken),
            'label' => 'Expired Android test',
            'expires_at' => now()->subMinute(),
        ]);

        $this->postJson('/api/vu-study/sync', [
            'items' => [[
                'item_type' => 'quiz',
                'title' => 'Must not save',
                'source_key' => 'expired-token-item',
            ]],
        ], [
            'Authorization' => 'Bearer ' . $plainToken,
        ])->assertUnauthorized();

        $this->assertDatabaseMissing('vu_study_items', [
            'source_key' => 'expired-token-item',
        ]);
    }
}
