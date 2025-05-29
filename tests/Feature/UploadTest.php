<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Jobs\ImageProcessor;

class UploadTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_upload_form_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/upload');

        $response->assertOk();
    }

    public function test_user_can_upload_image_and_dispatch_job()
    {
        Storage::fake('public');
        Queue::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->post(route('image.store'), [
            'image' => $file,
        ]);

        $response->assertRedirect(route('image.add'));

        // Assert that the job was dispatched and get the filename from it
        Queue::assertPushed(ImageProcessor::class, function ($job) use ($user) {
            $expectedPath = 'upload/' . $user->id;
            $this->assertStringStartsWith(storage_path('app/public/' . $expectedPath), $job->filepath);

            // Extract the relative path from full filepath
            $relativePath = str_replace(storage_path('app/public/'), '', $job->filepath);

            // Assert the file exists on the fake disk
            Storage::disk('public')->assertExists($relativePath);

            return true;
        });
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
}
