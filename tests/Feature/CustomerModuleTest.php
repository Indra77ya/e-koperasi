<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CustomerModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user and authenticate
        $user = factory(User::class)->create();
        $this->actingAs($user);
    }

    /** @test */
    public function it_can_display_customer_list()
    {
        factory(Customer::class, 5)->create();

        $response = $this->get(route('customers.index'));

        $response->assertStatus(200);
        $response->assertViewIs('customers.index');
    }

    /** @test */
    public function it_can_store_a_new_customer()
    {
        Storage::fake('public');

        $fileKtp = UploadedFile::fake()->image('ktp.jpg');
        $fileJaminan = UploadedFile::fake()->image('jaminan.jpg');

        $data = [
            'nik' => '1234567890',
            'nama' => 'John Doe',
            'alamat' => 'Jl. Test No. 123',
            'no_hp' => '08123456789',
            'pekerjaan' => 'Wiraswasta',
            'info_bisnis' => 'Jualan Bakso',
            'status_risiko' => 'safe',
            'file_ktp' => $fileKtp,
            'file_jaminan' => $fileJaminan,
        ];

        $response = $this->post(route('customers.store'), $data);

        $response->assertRedirect(route('customers.index'));
        $this->assertDatabaseHas('nasabah', [
            'nik' => '1234567890',
            'nama' => 'John Doe',
            'status_risiko' => 'safe',
        ]);

        // Check if files are stored (not checking exact path as it's hashed)
        $customer = Customer::where('nik', '1234567890')->first();
        Storage::disk('public')->assertExists($customer->file_ktp);
        Storage::disk('public')->assertExists($customer->file_jaminan);
    }

    /** @test */
    public function it_can_show_customer_details()
    {
        $customer = factory(Customer::class)->create();

        $response = $this->get(route('customers.show', $customer->id));

        $response->assertStatus(200);
        $response->assertSee($customer->nama);
        $response->assertSee($customer->nik);
    }

    /** @test */
    public function it_can_update_customer_details()
    {
        $customer = factory(Customer::class)->create();

        $data = [
            'nik' => $customer->nik, // Keep same NIK
            'nama' => 'Updated Name',
            'alamat' => 'Updated Address',
            'no_hp' => '08987654321',
            'pekerjaan' => 'Updated Job',
            'status_risiko' => 'warning',
        ];

        $response = $this->put(route('customers.update', $customer->id), $data);

        $response->assertRedirect(route('customers.index'));
        $this->assertDatabaseHas('nasabah', [
            'id' => $customer->id,
            'nama' => 'Updated Name',
            'status_risiko' => 'warning',
        ]);
    }

    /** @test */
    public function it_can_delete_a_customer()
    {
        $customer = factory(Customer::class)->create();

        $response = $this->delete(route('customers.destroy', $customer->id));

        $response->assertRedirect(route('customers.index'));
        $this->assertDatabaseMissing('nasabah', ['id' => $customer->id]);
    }
}
