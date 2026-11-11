<?php

namespace Tests\Feature;

use App\Models\CloudConnection;
use App\Models\CloudRemoteFile;
use App\Services\CloudStorageService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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
    public function test_remote_upload_saves_to_the_selected_connected_provider(): void
    {
        $user = User::factory()->create();
        $connection = CloudConnection::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_account_id' => 'google-account',
            'access_token' => 'access-token',
        ]);

        $storage = \\Mockery::mock(CloudStorageService::class);
        $storage->shouldReceive('upload')->once()->andReturn([
            'id' => 'drive-file-123',
            'name' => 'photo.png',
            'mime_type' => 'image/png',
            'size' => 1234,
            'modified_at' => now()->toIso8601String(),
        ]);
        $this->app->instance(CloudStorageService::class, $storage);

        $this->actingAs($user)->post(route('cloud.store'), [
            'file' => UploadedFile::fake()->image('photo.png'),
            'destination' => 'google',
            'category' => 'images',
        ])->assertRedirect(route('cloud.index'));

        $this->assertDatabaseHas('cloud_remote_files', [
            'user_id' => $user->id,
            'cloud_connection_id' => $connection->id,
            'provider' => 'google',
            'provider_file_id' => 'drive-file-123',
            'name' => 'photo.png',
        ]);
    }

    public function test_user_cannot_download_another_users_remote_file(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $connection = CloudConnection::create([
            'user_id' => $owner->id,
            'provider' => 'dropbox',
            'provider_account_id' => 'dbid:owner',
            'access_token' => 'owner-token',
        ]);
        $file = CloudRemoteFile::create([
            'user_id' => $owner->id,
            'cloud_connection_id' => $connection->id,
            'provider' => 'dropbox',
            'provider_file_id' => 'id:private-file',
            'name' => 'private.pdf',
            'mime_type' => 'application/pdf',
            'size' => 100,
            'category' => 'documents',
        ]);

        $this->actingAs($other)->get(route('cloud.remote.download', $file))->assertNotFound();
    }

    public function test_remote_image_preview_rejects_non_image_content(): void
    {
        $user = User::factory()->create();
        $connection = CloudConnection::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_account_id' => 'google-account',
            'access_token' => 'access-token',
        ]);
        $file = CloudRemoteFile::create([
            'user_id' => $user->id,
            'cloud_connection_id' => $connection->id,
            'provider' => 'google',
            'provider_file_id' => 'drive-pdf',
            'name' => 'document.pdf',
            'mime_type' => 'application/pdf',
            'size' => 100,
            'category' => 'documents',
        ]);

        $this->actingAs($user)->get(route('cloud.remote.preview', $file))->assertStatus(415);
    }

    public function test_expired_google_access_token_is_refreshed_before_remote_api_use(): void
    {
        Http::fake([
            'oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'fresh-google-token',
                'expires_in' => 3600,
                'scope' => 'https://www.googleapis.com/auth/drive.file',
            ], 200),
        ]);

        $user = User::factory()->create();
        $connection = CloudConnection::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_account_id' => 'google-refresh-test',
            'access_token' => 'expired-access-token',
            'refresh_token' => 'valid-refresh-token',
            'token_expires_at' => now()->subMinute(),
        ]);

        config([
            'services.cloud_storage.google.client_id' => 'client-id',
            'services.cloud_storage.google.client_secret' => 'client-secret',
        ]);

        $service = app(CloudStorageService::class);
        $this->assertSame('fresh-google-token', $service->accessToken($connection));

        Http::assertSent(fn ($request) => $request->url() === 'https://oauth2.googleapis.com/token'
            && $request['grant_type'] === 'refresh_token'
            && $request['refresh_token'] === 'valid-refresh-token');
        $this->assertSame('fresh-google-token', $connection->fresh()->access_token);
    }

}
