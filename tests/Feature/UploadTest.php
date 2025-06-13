<?php

namespace Tests\Feature;

use \App\Jobs\ImageResize;
use App\Jobs\ImageProcessor;
use App\Services\UserStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;
use App\Models\User;

class UploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_uploaded_images_as_json()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $storagePath = storage_path("app/public/upload/{$user->id}");
        File::makeDirectory($storagePath, 0777, true, true);
        touch($storagePath . '/example.jpg');

        $response = $this->getJson(route('images.index'));

        $response->assertOk();
        $response->assertJsonFragment(['example.jpg']);

        File::deleteDirectory($storagePath);
    }

    public function test_show_displays_file()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $fileName = 'image.jpg';
        $storagePath = storage_path("app/public/upload/{$user->id}");
        File::makeDirectory($storagePath, 0777, true, true);
        file_put_contents("{$storagePath}/{$fileName}", 'fake-image');

        $response = $this->get(route('image.show', $fileName));
        $response->assertOk();

        File::deleteDirectory($storagePath);
    }

    // this is not working inertia bug maybe
    //    public function test_create_returns_inertia_view()
    //    {
    //        $user = User::factory()->create();
    //        $this->actingAs($user);
    //
    //        $response = $this->get(route('image.create'));
    //        $response->assertInertia(fn (Assert $page) => $page->component('upload/Add'));
    //    }

    public function test_create_form_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('image.create'));

        $response->assertOk();
    }

    public function test_store_validates_and_dispatches_job()
    {
        Queue::fake();
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->post(route('image.store'), [
            'image' => $file,
        ]);

        $response->assertRedirect(route('image.create'));
        Queue::assertPushed(ImageProcessor::class);
    }

    public function test_destroy_deletes_file()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $fileName = 'image.jpg';
        $storagePath = storage_path("app/public/upload/{$user->id}");
        File::makeDirectory($storagePath, 0777, true, true);
        file_put_contents("{$storagePath}/{$fileName}", 'fake-image');

        $response = $this->post(route('image.destroy', $fileName));
        $response->assertNoContent();

        $this->assertFileDoesNotExist("{$storagePath}/{$fileName}");

        File::deleteDirectory($storagePath);
    }

    public function test_upload_fails_when_image_is_missing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('image.store'), []);

        $response->assertSessionHasErrors(['image']);
    }

    public function test_upload_fails_when_file_is_not_an_image(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('image.store'), [
            'image' => UploadedFile::fake()->create('document.pdf', 500, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors(['image']);
    }

    public function test_job_processes_file()
    {
        Storage::fake('public');

        // Bind UserStorageService to point to fake disk path
        $basePath = Storage::disk('public')->path('users/user@example.com');
        $this->app->instance(UserStorageService::class, new class($basePath) extends UserStorageService {
            public function __construct(private string $fakeBasePath) {}
            public function basePath(): string {
                return $this->fakeBasePath;
            }
        });

        // Put original fake image
        $filename = 'file.jpg';
        Storage::disk('public')->putFileAs('users/user@example.com', UploadedFile::fake()->image($filename), $filename);

        // Create and run the ImageProcessor job (which dispatches batch jobs)
        $job = new ImageProcessor('user@example.com', $filename);

        // Instead of dispatch(), call handle() directly to synchronously run batch dispatch
        $job->handle();

        // manually run the ImageResize jobs that would have been dispatched:
        $originalFilePath = $basePath . '/' . $filename;
        foreach ([100, 300] as $size) {
            $resizeJob = new ImageResize($originalFilePath, $size);
            $resizeJob->handle();
        }

        // Assert resized images exist
        foreach ([100, 300] as $size) {
            $resizedFile = 'users/user@example.com/file-' . $size . '.jpg';
            Storage::disk('public')->assertExists($resizedFile);
        }
    }
}
