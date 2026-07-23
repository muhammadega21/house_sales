<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\MarketingTarget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMarketingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $marketing;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', Role::Admin)->firstOrFail();
        $this->marketing = User::create(['nama_lengkap' => 'Marketing Satu', 'username' => 'mktsatu', 'password' => 'password123', 'no_hp' => '0812345', 'role' => Role::Marketing, 'status' => 'aktif', 'persentase_komisi' => 2.5]);
    }

    public function test_admin_can_create_marketing_and_non_admin_cannot_access_module(): void
    {
        $this->actingAs($this->admin)->post(route('admin.marketing.store'), ['nama_lengkap' => 'Marketing Baru', 'username' => 'mktbaru', 'password' => 'password123', 'no_hp' => '08123456789', 'persentase_komisi' => 2.5, 'status' => 'aktif'])->assertRedirect(route('admin.marketing.index'));
        $this->assertDatabaseHas('users', ['username' => 'mktbaru', 'role' => 'marketing']);
        $this->actingAs($this->marketing)->get(route('admin.marketing.index'))->assertRedirect(route('marketing.dashboard'));
    }

    public function test_target_is_updated_for_the_same_marketing_and_period(): void
    {
        $this->actingAs($this->admin)->post(route('admin.marketing.store-target', $this->marketing), ['id_marketing' => $this->marketing->id, 'periode_bulan' => 7, 'periode_tahun' => 2026, 'target_unit' => 5])->assertRedirect(route('admin.marketing.show', $this->marketing));
        $this->actingAs($this->admin)->post(route('admin.marketing.store-target', $this->marketing), ['id_marketing' => $this->marketing->id, 'periode_bulan' => 7, 'periode_tahun' => 2026, 'target_unit' => 8])->assertRedirect(route('admin.marketing.show', $this->marketing));
        $this->assertSame(1, MarketingTarget::count());
        $this->assertDatabaseHas('marketing_target', ['id_marketing' => $this->marketing->id, 'target_unit' => 8]);
    }

    public function test_destroy_deactivates_marketing_and_preserves_record(): void
    {
        $this->actingAs($this->admin)->delete(route('admin.marketing.destroy', $this->marketing))->assertRedirect(route('admin.marketing.index'));
        $this->assertDatabaseHas('users', ['id' => $this->marketing->id, 'status' => 'non_aktif']);
    }
}
