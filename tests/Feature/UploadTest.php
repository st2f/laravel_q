<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

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

//    public function test_avatars_can_be_uploaded(): void
//    {
//        Storage::fake('avatars');
//
//        $file = UploadedFile::fake()->image('avatar.jpg');
//
//        $response = $this->post('/avatar', [
//            'avatar' => $file,
//        ]);
//
//        Storage::disk('avatars')->assertExists($file->hashName());
//    }
}
