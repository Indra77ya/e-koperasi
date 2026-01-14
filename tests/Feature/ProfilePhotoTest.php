<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_profile_photo()
    {
        Storage::fake('public');

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->patch('/profile/' . $user->id, [
            'name' => 'New Name',
            'email' => $user->email,
            'photo' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertRedirect('/profile');

        $user->refresh();
        $this->assertNotNull($user->photo);
        Storage::disk('public')->assertExists($user->photo);
    }
}
