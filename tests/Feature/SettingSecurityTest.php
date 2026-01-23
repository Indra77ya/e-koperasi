<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingSecurityTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test that arbitrary keys are ignored (Fix Verification).
     */
    public function test_arbitrary_keys_are_ignored()
    {
        $user = factory(User::class)->create();

        // Use a random key to avoid collision
        $key = 'hacked_key_' . uniqid();
        $value = 'hacked_value';

        $response = $this->actingAs($user)
                         ->post(route('settings.update'), [
                             $key => $value,
                         ]);

        // The controller redirects back with success even if nothing was updated (because validation passed for empty input if all are ignored)
        // Or if we sent other valid data.
        // Here we send ONLY the hacked key.
        // Validation will return empty array. Loop runs 0 times. Returns success.
        $response->assertSessionHas('success');

        // Assert vulnerability is GONE: key is NOT saved
        $savedValue = Setting::get($key);
        $this->assertNull($savedValue, "Arbitrary key '$key' SHOULD NOT be saved.");
    }

    /**
     * Test that invalid values are rejected (Fix Verification).
     */
    public function test_invalid_values_are_rejected()
    {
        $user = factory(User::class)->create();

        $key = 'default_interest_rate';
        $invalidValue = -5;

        $response = $this->actingAs($user)
                         ->post(route('settings.update'), [
                             $key => $invalidValue,
                         ]);

        // Expect validation errors
        $response->assertSessionHasErrors([$key]);

        // Assert vulnerability is GONE: invalid value is NOT saved
        $this->assertNotEquals($invalidValue, Setting::get($key), "Invalid value SHOULD NOT be saved.");
    }
}
