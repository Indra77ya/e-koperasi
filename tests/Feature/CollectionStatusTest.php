<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CollectionStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_html_badge_for_status()
    {
        // Create user
        $user = factory(User::class)->create();

        // Create loan with 'macet' status
        $loan = Loan::create([
            'kode_pinjaman' => 'P-TEST-001',
            'anggota_id' => null,
            'nasabah_id' => null,
            'jenis_pinjaman' => 'Biasa',
            'jumlah_pinjaman' => 1000000,
            'tenor' => 12,
            'suku_bunga' => 10,
            'satuan_bunga' => 'tahun',
            'tempo_angsuran' => 'bulanan',
            'jenis_bunga' => 'flat',
            'biaya_admin' => 0,
            'denda_keterlambatan' => 0,
            'tanggal_pengajuan' => now(),
            'status' => 'macet',
            'kolektabilitas' => 'Macet',
            'keterangan' => 'Test Loan',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('collections.data', ['kolektabilitas' => 'Macet']));

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertNotEmpty($data['data']);

        $status = $data['data'][0]['status'];

        // Now it should contain the HTML badge
        $this->assertStringContainsString('<span class="badge badge-danger">Macet</span>', $status);
    }
}
