<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\StatusUnit;
use App\Models\Perumahan;
use App\Models\UnitRumah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUnitRumahCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Perumahan $perumahan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', Role::Admin)->firstOrFail();
        $this->perumahan = Perumahan::create([
            'nama_perumahan' => 'Griya Aktif', 'alamat' => 'Jl. Utama',
            'kota' => 'Palembang', 'provinsi' => 'Sumatera Selatan', 'status' => 'aktif',
        ]);
    }

    public function test_admin_can_create_unit_and_total_is_incremented(): void
    {
        $this->actingAs($this->admin)->post(route('admin.unit-rumah.store'), $this->unitPayload())
            ->assertRedirect(route('admin.unit-rumah.index'));

        $this->assertDatabaseHas('unit_rumah', ['id_perumahan' => $this->perumahan->id, 'kode_unit' => 'A-01']);
        $this->assertDatabaseHas('perumahan', ['id' => $this->perumahan->id, 'total_unit' => 1]);
    }

    public function test_code_unit_may_repeat_in_different_perumahan_but_not_same_perumahan(): void
    {
        UnitRumah::create($this->unitPayload());
        $other = Perumahan::create(['nama_perumahan' => 'Griya Dua', 'alamat' => 'Jl. Dua', 'kota' => 'Palembang', 'provinsi' => 'Sumatera Selatan', 'status' => 'aktif']);
        UnitRumah::create([...$this->unitPayload(), 'id_perumahan' => $other->id]);

        $this->actingAs($this->admin)->from(route('admin.unit-rumah.create'))->post(route('admin.unit-rumah.store'), $this->unitPayload())
            ->assertRedirect(route('admin.unit-rumah.create'))->assertSessionHasErrors('kode_unit');
    }

    public function test_sold_unit_cannot_be_updated_and_unavailable_unit_cannot_be_deleted(): void
    {
        $unit = UnitRumah::create([...$this->unitPayload(), 'status_unit' => StatusUnit::Dijual->value]);

        $this->actingAs($this->admin)->put(route('admin.unit-rumah.update', $unit), $this->unitPayload())
            ->assertSessionHasErrors('unit');
        $this->actingAs($this->admin)->delete(route('admin.unit-rumah.destroy', $unit))
            ->assertSessionHas('warning');
    }

    private function unitPayload(): array
    {
        return [
            'id_perumahan' => $this->perumahan->id, 'kode_unit' => 'A-01', 'tipe_rumah' => '36/60',
            'kategori' => 'subsidi', 'jenis_ketersediaan' => 'ready_stock', 'luas_tanah' => 60,
            'luas_bangunan' => 36, 'harga_jual' => 185000000,
        ];
    }
}
