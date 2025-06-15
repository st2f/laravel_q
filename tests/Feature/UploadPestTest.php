<?php

use App\Jobs\ImageProcessor;
use App\Jobs\ImageResize;
use App\Models\User;
use App\Services\UserStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('[pest] index returns uploaded images as json', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $storagePath = storage_path("app/public/upload/{$user->id}");
    File::makeDirectory($storagePath, 0777, true, true);
    touch($storagePath . '/example.jpg');

    $response = $this->getJson(route('images.index'));

    $response->assertOk();
    $response->assertJsonFragment(['example.jpg']);

    File::deleteDirectory($storagePath);
});

test('[pest] show displays file', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $fileName = 'image.jpg';
    $storagePath = storage_path("app/public/upload/{$user->id}");
    File::makeDirectory($storagePath, 0777, true, true);
    file_put_contents("{$storagePath}/{$fileName}", 'fake-image');

    $response = $this->get(route('image.show', $fileName));
    $response->assertOk();

    File::deleteDirectory($storagePath);
});

test('[pest] create form is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('image.create'));

    $response->assertOk();
});

test('[pest] store validates and dispatches job', function () {
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
});

test('[pest] destroy deletes file', function () {
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
});

test('[pest] upload fails when image is missing', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('image.store'), []);

    $response->assertSessionHasErrors(['image']);
});

test('[pest] upload fails when file is not an image', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('image.store'), [
        'image' => UploadedFile::fake()->create('document.pdf', 500, 'application/pdf'),
    ]);

    $response->assertSessionHasErrors(['image']);
});

test('[pest] job processes file', function () {
    Storage::fake('public');

    // Bind UserStorageService to point to fake disk path
    $basePath = Storage::disk('public')->path('users/user@example.com');
    $this->app->instance(UserStorageService::class, new class($basePath) extends UserStorageService {
        function __construct(private string $fakeBasePath)
        {
        }
        function basePath(): string
        {
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
});
