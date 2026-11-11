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
            'refresh_token' => 'refresh-token',
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

        $storage = \Mockery::mock(CloudStorageService::class);
        $storage->shouldReceive('upload')->once()->andReturn([
            'id' => 'drive-file-123',
            'name' => 'photo.png',
            'mime_type' => 'image/png',
            'size' => 1234,
            'modified_at' => now()->toIso8601String(),
        ]);
        $this->app->instance(CloudStorageService::class, $storage);

        $this->actingAs($user)->post(route('cloud.store'), [
            'file' => UploadedFile::fake()->create('photo.png', 20, 'image/png'),
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


    public function test_failed_profile_lookup_revokes_new_google_grant(): void
    {
        Http::fake([
            'oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'new-access-token',
                'refresh_token' => 'new-refresh-token',
                'expires_in' => 3600,
            ], 200),
            'www.googleapis.com/oauth2/v2/userinfo' => Http::response(['error' => 'unavailable'], 503),
            'oauth2.googleapis.com/revoke' => Http::response(null, 200),
        ]);

        config([
            'services.cloud_storage.google.client_id' => 'client-id',
            'services.cloud_storage.google.client_secret' => 'client-secret',
            'services.cloud_storage.google.redirect' => 'https://example.test/cloud/callback/google',
        ]);

        $user = User::factory()->create();
        $this->actingAs($user)->withSession(['cloud_oauth_state_google' => 'valid-state'])
            ->get(route('cloud.callback', ['provider' => 'google', 'state' => 'valid-state', 'code' => 'authorization-code']))
            ->assertRedirect(route('cloud.index'))
            ->assertSessionHas('error');

        Http::assertSent(fn ($request) => $request->url() === 'https://oauth2.googleapis.com/revoke'
            && $request['token'] === 'new-refresh-token');
        $this->assertDatabaseCount('cloud_connections', 0);
    }

    public function test_dropbox_disconnect_refreshes_expired_access_token_before_revoking(): void
    {
        Http::fake([
            'api.dropbox.com/oauth2/token' => Http::response([
                'access_token' => 'fresh-access-token',
                'expires_in' => 3600,
            ], 200),
            'api.dropboxapi.com/2/auth/token/revoke' => Http::response(null, 200),
        ]);

        config([
            'services.cloud_storage.dropbox.client_id' => 'dropbox-key',
            'services.cloud_storage.dropbox.client_secret' => 'dropbox-secret',
        ]);

        $user = User::factory()->create();
        CloudConnection::create([
            'user_id' => $user->id,
            'provider' => 'dropbox',
            'provider_account_id' => 'dbid:expired',
            'access_token' => 'expired-access-token',
            'refresh_token' => 'dropbox-refresh-token',
            'token_expires_at' => now()->subMinute(),
        ]);

        $this->actingAs($user)->delete(route('cloud.disconnect', 'dropbox'))
            ->assertRedirect(route('cloud.index'))
            ->assertSessionHas('success');

        Http::assertSent(fn ($request) => $request->url() === 'https://api.dropboxapi.com/2/auth/token/revoke'
            && $request->hasHeader('Authorization', 'Bearer fresh-access-token'));
        $this->assertDatabaseCount('cloud_connections', 0);
    }


    public function test_google_drive_listing_follows_all_next_page_tokens(): void
    {
        Http::fake([
            'www.googleapis.com/drive/v3/files*' => Http::sequence()
                ->push([
                    'files' => [['id' => 'google-file-1', 'name' => 'first.pdf', 'mimeType' => 'application/pdf']],
                    'nextPageToken' => 'page-two-token',
                ], 200)
                ->push([
                    'files' => [['id' => 'google-file-2', 'name' => 'second.pdf', 'mimeType' => 'application/pdf']],
                ], 200),
        ]);

        $user = User::factory()->create();
        $connection = CloudConnection::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_account_id' => 'google-pagination',
            'access_token' => 'valid-token',
            'token_expires_at' => now()->addHour(),
        ]);

        $files = app(CloudStorageService::class)->listFiles($connection);

        $this->assertSame(['google-file-1', 'google-file-2'], array_column($files, 'id'));
        Http::assertSentCount(2);
        Http::assertSent(function ($request) {
            parse_str($request->toPsrRequest()->getUri()->getQuery(), $query);

            return $request->url() === 'https://www.googleapis.com/drive/v3/files'
                && ($query['pageToken'] ?? null) === 'page-two-token';
        });
    }

    public function test_dropbox_listing_continues_until_has_more_is_false(): void
    {
        Http::fake(function ($request) {
            if ($request->url() === 'https://api.dropboxapi.com/2/files/list_folder') {
                return Http::response([
                    'entries' => [['.tag' => 'file', 'id' => 'db-file-1', 'name' => 'first.pdf', 'size' => 10]],
                    'cursor' => 'cursor-one',
                    'has_more' => true,
                ], 200);
            }

            if ($request->url() === 'https://api.dropboxapi.com/2/files/list_folder/continue') {
                if (($request['cursor'] ?? null) === 'cursor-one') {
                    return Http::response([
                        'entries' => [['.tag' => 'file', 'id' => 'db-file-2', 'name' => 'second.pdf', 'size' => 20]],
                        'cursor' => 'cursor-two',
                        'has_more' => true,
                    ], 200);
                }

                return Http::response([
                    'entries' => [['.tag' => 'file', 'id' => 'db-file-3', 'name' => 'third.pdf', 'size' => 30]],
                    'cursor' => 'cursor-three',
                    'has_more' => false,
                ], 200);
            }

            return Http::response([], 404);
        });

        $user = User::factory()->create();
        $connection = CloudConnection::create([
            'user_id' => $user->id,
            'provider' => 'dropbox',
            'provider_account_id' => 'dbid:pagination',
            'access_token' => 'valid-token',
            'token_expires_at' => now()->addHour(),
        ]);

        $files = app(CloudStorageService::class)->listFiles($connection);

        $this->assertSame(['db-file-1', 'db-file-2', 'db-file-3'], array_column($files, 'id'));
        Http::assertSentCount(3);
    }

    public function test_remote_sync_preserves_existing_user_category_and_infers_category_for_new_files(): void
    {
        Http::fake([
            'www.googleapis.com/drive/v3/files*' => Http::response([
                'files' => [
                    ['id' => 'existing-image', 'name' => 'chosen-category.png', 'mimeType' => 'image/png', 'size' => '100'],
                    ['id' => 'new-image', 'name' => 'new-image.png', 'mimeType' => 'image/png', 'size' => '200'],
                ],
            ], 200),
        ]);

        $user = User::factory()->create();
        $connection = CloudConnection::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_account_id' => 'google-category-test',
            'access_token' => 'valid-token',
            'token_expires_at' => now()->addHour(),
        ]);
        $existing = CloudRemoteFile::create([
            'user_id' => $user->id,
            'cloud_connection_id' => $connection->id,
            'provider' => 'google',
            'provider_file_id' => 'existing-image',
            'name' => 'chosen-category.png',
            'mime_type' => 'image/png',
            'size' => 90,
            'category' => 'other',
        ]);

        $this->actingAs($user)->post(route('cloud.remote.sync'))
            ->assertRedirect(route('cloud.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('cloud_remote_files', [
            'id' => $existing->id,
            'category' => 'other',
            'name' => 'chosen-category.png',
        ]);
        $this->assertDatabaseHas('cloud_remote_files', [
            'cloud_connection_id' => $connection->id,
            'provider_file_id' => 'new-image',
            'category' => 'images',
        ]);
    }

    public function test_oauth_cannot_replace_a_different_connected_account_or_its_remote_file_records(): void
    {
        Http::fake([
            'oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'new-account-access-token',
                'refresh_token' => 'new-account-refresh-token',
                'expires_in' => 3600,
            ], 200),
            'www.googleapis.com/oauth2/v2/userinfo' => Http::response([
                'id' => 'different-google-account',
                'email' => 'different@example.com',
            ], 200),
            'oauth2.googleapis.com/revoke' => Http::response(null, 200),
        ]);

        config([
            'services.cloud_storage.google.client_id' => 'client-id',
            'services.cloud_storage.google.client_secret' => 'client-secret',
            'services.cloud_storage.google.redirect' => 'https://example.test/cloud/callback/google',
        ]);

        $user = User::factory()->create();
        $connection = CloudConnection::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_account_id' => 'original-google-account',
            'display_name' => 'original@example.com',
            'access_token' => 'original-access-token',
            'refresh_token' => 'original-refresh-token',
        ]);
        $remoteFile = CloudRemoteFile::create([
            'user_id' => $user->id,
            'cloud_connection_id' => $connection->id,
            'provider' => 'google',
            'provider_file_id' => 'original-drive-file',
            'name' => 'preserved.pdf',
            'mime_type' => 'application/pdf',
            'size' => 100,
            'category' => 'documents',
        ]);

        $this->actingAs($user)->withSession(['cloud_oauth_state_google' => 'valid-state'])
            ->get(route('cloud.callback', [
                'provider' => 'google',
                'state' => 'valid-state',
                'code' => 'new-account-authorization-code',
            ]))
            ->assertRedirect(route('cloud.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('cloud_connections', [
            'id' => $connection->id,
            'provider_account_id' => 'original-google-account',
            'access_token' => $connection->getRawOriginal('access_token'),
        ]);
        $this->assertDatabaseHas('cloud_remote_files', [
            'id' => $remoteFile->id,
            'provider_file_id' => 'original-drive-file',
            'cloud_connection_id' => $connection->id,
        ]);

        Http::assertSent(fn ($request) => $request->url() === 'https://oauth2.googleapis.com/revoke'
            && $request['token'] === 'new-account-refresh-token');
        $this->assertDatabaseCount('cloud_connections', 1);
    }

}
