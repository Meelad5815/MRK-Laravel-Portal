<?php

namespace Tests\Feature;

use App\Models\CloudFile;
use App\Models\CloudFolder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PersonalCloudTest extends TestCase
{
    use RefreshDatabase;

    public function test_personal_cloud_requires_authentication(): void
    {
        $this->get(route('cloud.index'))->assertRedirect(route('login'));
        $this->post(route('cloud.store'))->assertRedirect(route('login'));
        $this->post(route('cloud.folders.store'))->assertRedirect(route('login'));
    }

    public function test_user_can_upload_list_and_download_their_own_file(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('cloud.store'), [
            'file' => UploadedFile::fake()->create('study-notes.pdf', 120, 'application/pdf'),
        ])->assertRedirect(route('cloud.index'));

        $file = CloudFile::query()->where('user_id', $user->id)->firstOrFail();
        Storage::disk('local')->assertExists($file->stored_path);

        $this->actingAs($user)->get(route('cloud.index'))
            ->assertOk()
            ->assertSee('study-notes.pdf');

        $this->actingAs($user)->get(route('cloud.download', $file))
            ->assertOk()
            ->assertHeader('content-disposition');
    }

    public function test_user_can_preview_an_owned_image_inline(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $path = 'mrk-cloud/'.$user->id.'/photo.png';
        Storage::disk('local')->put($path, 'fake-image-bytes');
        $file = CloudFile::query()->create([
            'user_id' => $user->id,
            'original_name' => 'photo.png',
            'stored_path' => $path,
            'mime_type' => 'image/png',
            'extension' => 'png',
            'size' => 16,
        ]);

        $this->actingAs($user)->get(route('cloud.preview', $file))
            ->assertOk()
            ->assertHeader('content-type', 'image/png')
            ->assertHeader('content-disposition');
    }

    public function test_user_cannot_preview_another_users_image(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $path = 'mrk-cloud/'.$owner->id.'/secret.png';
        Storage::disk('local')->put($path, 'private');
        $file = CloudFile::query()->create([
            'user_id' => $owner->id,
            'original_name' => 'secret.png',
            'stored_path' => $path,
            'mime_type' => 'image/png',
            'extension' => 'png',
            'size' => 7,
        ]);

        $this->actingAs($other)->get(route('cloud.preview', $file))->assertNotFound();
    }

    public function test_non_image_files_are_not_rendered_as_inline_previews(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $path = 'mrk-cloud/'.$user->id.'/document.pdf';
        Storage::disk('local')->put($path, 'private');
        $file = CloudFile::query()->create([
            'user_id' => $user->id,
            'original_name' => 'document.pdf',
            'stored_path' => $path,
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'size' => 7,
        ]);

        $this->actingAs($user)->get(route('cloud.preview', $file))->assertStatus(415);
    }

    public function test_user_cannot_list_download_or_delete_another_users_file(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $path = 'mrk-cloud/'.$owner->id.'/private-file.pdf';
        Storage::disk('local')->put($path, 'private content');

        $file = CloudFile::query()->create([
            'user_id' => $owner->id,
            'original_name' => 'private-file.pdf',
            'stored_path' => $path,
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'size' => 15,
        ]);

        $this->actingAs($otherUser)->get(route('cloud.index'))
            ->assertOk()
            ->assertDontSee('private-file.pdf');

        $this->actingAs($otherUser)->get(route('cloud.download', $file))->assertNotFound();
        $this->actingAs($otherUser)->delete(route('cloud.destroy', $file))->assertNotFound();

        $this->assertDatabaseHas('cloud_files', ['id' => $file->id, 'user_id' => $owner->id]);
        Storage::disk('local')->assertExists($path);
    }

    public function test_personal_cloud_rejects_files_larger_than_five_megabytes(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('cloud.store'), [
            'file' => UploadedFile::fake()->create('oversized.zip', 5121, 'application/zip'),
        ])->assertSessionHasErrors('file');

        $this->assertDatabaseCount('cloud_files', 0);
    }

    public function test_user_can_create_folder_and_upload_file_into_it(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('cloud.folders.store'), [
            'name' => 'Study Notes',
        ])->assertRedirect(route('cloud.index'));

        $folder = CloudFolder::query()->where('user_id', $user->id)->firstOrFail();

        $this->actingAs($user)->post(route('cloud.store'), [
            'file' => UploadedFile::fake()->create('lecture.pdf', 100, 'application/pdf'),
            'folder_id' => $folder->id,
            'category' => 'documents',
        ])->assertRedirect(route('cloud.index'));

        $file = CloudFile::query()->where('user_id', $user->id)->firstOrFail();
        $this->assertSame($folder->id, $file->cloud_folder_id);
        $this->assertSame('documents', $file->category);

        $this->actingAs($user)->get(route('cloud.index', ['folder' => $folder->id, 'category' => 'documents']))
            ->assertOk()
            ->assertSee('lecture.pdf')
            ->assertSee('Study Notes');
    }

    public function test_user_cannot_upload_file_into_another_users_folder(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $folder = CloudFolder::query()->create(['user_id' => $owner->id, 'name' => 'Private']);

        $this->actingAs($otherUser)->post(route('cloud.store'), [
            'file' => UploadedFile::fake()->create('attempt.pdf', 100, 'application/pdf'),
            'folder_id' => $folder->id,
            'category' => 'documents',
        ])->assertSessionHasErrors('folder_id');

        $this->assertDatabaseCount('cloud_files', 0);
    }

    public function test_folder_name_must_be_a_string(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('cloud.folders.store'), ['name' => ['invalid']])
            ->assertSessionHasErrors('name');

        $this->assertDatabaseCount('cloud_folders', 0);
    }

    public function test_folder_names_are_unique_per_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('cloud.folders.store'), ['name' => 'Work'])
            ->assertRedirect(route('cloud.index'));
        $this->actingAs($user)->post(route('cloud.folders.store'), ['name' => 'Work'])
            ->assertSessionHasErrors('name');

        $this->assertDatabaseCount('cloud_folders', 1);
    }
}
