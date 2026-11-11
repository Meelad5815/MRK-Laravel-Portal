<?php

namespace Tests\Feature;

use App\Models\CloudConnection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CloudConnectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_cloud_provider_connect_requires_authentication(): void
    {
        $this->get(route('cloud.connect', 'google'))->assertRedirect(route('login'));
    }

    public function test_provider_connection_requires_owner_configured_oauth_credentials(): void
    {
        config(['services.cloud_storage.google.client_id' => null]);
        $user = User::factory()->create();

        $this->actingAs($user)->from(route('cloud.index'))
            ->get(route('cloud.connect', 'google'))
            ->assertRedirect(route('cloud.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('cloud_connections', 0);
    }

    public function test_oauth_callback_rejects_invalid_state(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->withSession(['cloud_oauth_state_google' => 'expected-state'])
            ->get(route('cloud.callback', ['provider' => 'google', 'state' => 'wrong-state', 'code' => 'not-a-real-code']))
            ->assertRedirect(route('cloud.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('cloud_connections', 0);
    }

    public function test_provider_tokens_are_encrypted_at_rest(): void
    {
        $user = User::factory()->create();
        $connection = CloudConnection::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_account_id' => 'account-123',
            'display_name' => 'user@example.com',
            'access_token' => 'plain-access-token',
            'refresh_token' => 'plain-refresh-token',
            'scopes' => ['openid', 'email'],
        ]);

        $this->assertNotSame('plain-access-token', $connection->getRawOriginal('access_token'));
        $this->assertNotSame('plain-refresh-token', $connection->getRawOriginal('refresh_token'));
        $this->assertSame('plain-access-token', $connection->access_token);
        $this->assertSame('plain-refresh-token', $connection->refresh_token);
    }

    public function test_disconnect_revokes_dropbox_access_before_deleting_local_tokens(): void
    {
        Http::fake([
            'api.dropboxapi.com/2/auth/token/revoke' => Http::response(null, 200),
        ]);

        $user = User::factory()->create();
        CloudConnection::create([
            'user_id' => $user->id,
            'provider' => 'dropbox',
            'provider_account_id' => 'dbid:abc',
            'access_token' => 'access-token',
        ]);

        $this->actingAs($user)->delete(route('cloud.disconnect', 'dropbox'))
            ->assertRedirect(route('cloud.index'))
            ->assertSessionHas('success');

        Http::assertSent(fn ($request) => $request->url() === 'https://api.dropboxapi.com/2/auth/token/revoke'
            && $request->hasHeader('Authorization', 'Bearer access-token'));
        $this->assertDatabaseCount('cloud_connections', 0);
    }

    public function test_disconnect_keeps_local_connection_when_provider_revocation_fails(): void
    {
        Http::fake([
            'oauth2.googleapis.com/revoke' => Http::response('temporary failure', 503),
        ]);

        $user = User::factory()->create();
        CloudConnection::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_account_id' => 'account-456',
            'access_token' => 'google-access-token',
            'refresh_token' => 'google-refresh-token',
        ]);

        $this->actingAs($user)->delete(route('cloud.disconnect', 'google'))
            ->assertRedirect(route('cloud.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('cloud_connections', 1);
    }
}
