<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Perumahan;
use App\Models\UnitRumah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPerumahanCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', Role::Admin)->firstOrFail();
    }

    public function test_only_admin_can_access_perumahan_index(): void
    {
        $this->get(route('admin.perumahan.index'))->assertRedirect(route('login'));

        $marketing = User::where('role', Role::Marketing)->firstOrFail();
        $this->actingAs($marketing)
            ->get(route('admin.perumahan.index'))
            ->assertRedirect(route('marketing.dashboard'));

        $this->actingAs($this->admin)
            ->get(route('admin.perumahan.index'))
            ->assertOk();
    }

    public function test_admin_can_create_perumahan_with_photo(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
            ->post(route('admin.perumahan.store'), [
                'nama_perumahan' => 'Griya Sejahtera',
                'alamat' => 'Jl. Sejahtera No. 1',
                'kota' => 'Palembang',
                'provinsi' => 'Sumatera Selatan',
                'kode_pos' => '30111',
                'status' => 'aktif',
                'foto_kawasan' => UploadedFile::fake()->image('kawasan.jpg'),
            ])
            ->assertRedirect(route('admin.perumahan.index'));

        $perumahan = Perumahan::where('nama_perumahan', 'Griya Sejahtera')->firstOrFail();
        Storage::disk('public')->assertExists($perumahan->foto_kawasan);
    }

    public function test_perumahan_with_units_is_deactivated_instead_of_deleted(): void
    {
        $perumahan = Perumahan::create([
            'nama_perumahan' => 'Griya Utama', 'alamat' => 'Jl. Utama', 'kota' => 'Palembang',
            'provinsi' => 'Sumatera Selatan', 'status' => 'aktif',
        ]);
        UnitRumah::create([
            'id_perumahan' => $perumahan->id, 'kode_unit' => 'A-01', 'tipe_rumah' => '36/60',
            'kategori' => 'subsidi', 'jenis_ketersediaan' => 'ready_stock', 'luas_tanah' => 60,
            'luas_bangunan' => 36, 'harga_jual' => 150000000, 'status_unit' => 'tersedia',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.perumahan.destroy', $perumahan))
            ->assertSessionHas('warning');

        $this->assertDatabaseHas('perumahan', ['id' => $perumahan->id, 'status' => 'non_aktif']);
    }
}