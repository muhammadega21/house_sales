<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProspekCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_can_create_prospek_and_it_is_linked_to_their_account(): void
    {
        $this->seed();

        $marketing = User::where('role', Role::Marketing->value)->first();

        $response = $this->actingAs($marketing)
            ->post(route('marketing.prospek.store'), [
                'nama_prospek' => 'Budi Santoso',
                'no_hp' => '081234567890',
                'email' => 'budi@example.com',
                'sumber_prospek' => 'instagram',
                'catatan' => 'Calon pembeli dari Instagram',
                'status_prospek' => 'baru',
                'tanggal_prospek' => now()->toDateString(),
            ]);

        $response->assertRedirect(route('marketing.prospek.index'));
        $this->assertDatabaseHas('prospek', [
            'id_marketing' => $marketing->id,
            'nama_prospek' => 'Budi Santoso',
            'no_hp' => '081234567890',
            'status_prospek' => 'baru',
        ]);
    }
}
