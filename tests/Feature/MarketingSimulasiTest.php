<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\MetodePembayaran;
use App\Models\Perumahan;
use App\Models\SimulasiPembayaran;
use App\Models\UnitRumah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingSimulasiTest extends TestCase
{
    use RefreshDatabase;

    private function createMarketingUser(): User
    {
        return User::create([
            'nama_lengkap' => 'Test Marketing',
            'username' => 'marketing' . rand(1000, 9999),
            'email' => 'marketing' . rand(1000, 9999) . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'marketing',
            'status' => 'aktif',
        ]);
    }

    private function createAvailableUnit(): UnitRumah
    {
        $perumahan = Perumahan::create([
            'nama_perumahan' => 'Test Perumahan',
            'alamat' => 'Test Alamat',
            'kota' => 'Test Kota',
            'provinsi' => 'Test Provinsi',
            'kode_pos' => '12345',
            'total_unit' => 10,
            'status' => 'aktif',
        ]);

        return UnitRumah::create([
            'id_perumahan' => $perumahan->id,
            'kode_unit' => 'TEST-' . rand(1000, 9999),
            'tipe_rumah' => 'Tipe 36',
            'kategori' => 'non_subsidi',
            'jenis_ketersediaan' => 'ready_stock',
            'luas_tanah' => 36,
            'luas_bangunan' => 36,
            'harga_jual' => 100_000_000,
            'status_unit' => 'tersedia',
        ]);
    }

    public function test_hitung_cash_keras_does_not_throw_tenor_error(): void
    {
        $user = $this->createMarketingUser();
        $unit = $this->createAvailableUnit();

        $response = $this->actingAs($user)->postJson('/marketing/simulasi/hitung', [
            'id_unit' => $unit->id,
            'metode_pembayaran' => MetodePembayaran::CashKeras->value,
            'dp_persen' => 0,
            'tenor_tahun' => null,
            'suku_bunga' => null,
            'diskon_persen' => 0,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'hasil' => [
                    'metode' => MetodePembayaran::CashKeras->value,
                ],
            ])
            ->assertJsonPath('perbandingan.kpr.metode', MetodePembayaran::Kpr->value)
            ->assertJsonPath('perbandingan.cash_bertahap.metode', MetodePembayaran::CashBertahap->value)
            ->assertJsonPath('perbandingan.cash_keras.metode', MetodePembayaran::CashKeras->value);
    }

    public function test_simpan_simulasi_returns_json_when_ajax(): void
    {
        $user = $this->createMarketingUser();
        $unit = $this->createAvailableUnit();

        $response = $this->actingAs($user)->postJson('/marketing/simulasi/simpan', [
            'id_unit' => $unit->id,
            'metode_pembayaran' => MetodePembayaran::CashKeras->value,
            'dp_persen' => 0,
            'tenor_tahun' => null,
            'suku_bunga' => null,
            'diskon_persen' => 0,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Hasil simulasi berhasil disimpan.',
            ]);

        $this->assertDatabaseCount('simulasi_pembayaran', 1);
        $this->assertDatabaseHas('simulasi_pembayaran', [
            'id_unit' => $unit->id,
            'id_marketing' => $user->id,
            'metode_pembayaran' => MetodePembayaran::CashKeras->value,
        ]);
    }

    public function test_simpan_simulasi_kpr_returns_json_when_ajax(): void
    {
        $user = $this->createMarketingUser();
        $unit = $this->createAvailableUnit();

        $response = $this->actingAs($user)->postJson('/marketing/simulasi/simpan', [
            'id_unit' => $unit->id,
            'metode_pembayaran' => MetodePembayaran::Kpr->value,
            'dp_persen' => 10,
            'tenor_tahun' => 15,
            'suku_bunga' => 8,
            'diskon_persen' => null,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Hasil simulasi berhasil disimpan.',
            ]);

        $this->assertDatabaseCount('simulasi_pembayaran', 1);
        $this->assertDatabaseHas('simulasi_pembayaran', [
            'id_unit' => $unit->id,
            'id_marketing' => $user->id,
            'metode_pembayaran' => MetodePembayaran::Kpr->value,
            'dp_persen' => 10,
            'tenor_tahun' => 15,
            'suku_bunga' => 8,
        ]);
    }

    public function test_export_pdf_returns_download_for_cash_keras(): void
    {
        $user = $this->createMarketingUser();
        $unit = $this->createAvailableUnit();

        $response = $this->actingAs($user)->get('/marketing/simulasi/export-pdf?' . http_build_query([
            'id_unit' => $unit->id,
            'dp_persen' => 0,
            'tenor_tahun' => 15,
            'suku_bunga' => 8,
            'diskon_persen' => 0,
        ]));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'attachment; filename=perbandingan-simulasi-' . strtolower($unit->kode_unit) . '.pdf');
    }

    public function test_export_pdf_works_for_cash_keras_without_tenor_and_bunga(): void
    {
        $user = $this->createMarketingUser();
        $unit = $this->createAvailableUnit();

        $response = $this->actingAs($user)->get('/marketing/simulasi/export-pdf?' . http_build_query([
            'id_unit' => $unit->id,
            'dp_persen' => 0,
            'diskon_persen' => 0,
        ]));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'attachment; filename=perbandingan-simulasi-' . strtolower($unit->kode_unit) . '.pdf');
    }

    public function test_perbandingan_works_for_cash_keras_without_tenor_and_bunga(): void
    {
        $user = $this->createMarketingUser();
        $unit = $this->createAvailableUnit();

        $response = $this->actingAs($user)->get('/marketing/simulasi/perbandingan?' . http_build_query([
            'id_unit' => $unit->id,
            'dp_persen' => 0,
            'diskon_persen' => 0,
        ]));

        $response->assertOk()
            ->assertViewIs('marketing.simulasi.perbandingan')
            ->assertViewHasAll(['hasilKpr', 'hasilCashBertahap', 'hasilCashKeras']);
    }
}
