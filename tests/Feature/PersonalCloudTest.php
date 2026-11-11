<?php

namespace Tests\Feature;

use App\Models\CloudFile;
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
}
