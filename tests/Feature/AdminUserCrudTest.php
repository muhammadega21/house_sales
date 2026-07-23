<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminUserCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $marketing;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', Role::Admin)->first();
        $this->marketing = User::where('role', Role::Marketing)->first();
    }

    public function test_only_admin_can_access_users_index(): void
    {
        // Unauthenticated redirects to login
        $this->get(route('admin.users.index'))
            ->assertRedirect(route('login'));

        // Marketing redirects to marketing dashboard (due to RoleMiddleware)
        $this->actingAs($this->marketing)
            ->get(route('admin.users.index'))
            ->assertRedirect(route('marketing.dashboard'));

        // Admin gets 200 OK
        $this->actingAs($this->admin)
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    public function test_admin_can_search_and_filter_users(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['search' => 'Budi']));
        $response->assertOk();
        $users = $response->viewData('users');
        $this->assertTrue($users->contains('username', 'marketing1'));
        $this->assertFalse($users->contains('username', 'manajemen'));

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['role' => 'marketing']));
        $response->assertOk();
        $users = $response->viewData('users');
        $this->assertTrue($users->contains('username', 'marketing1'));
        $this->assertFalse($users->contains('username', 'admin'));

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['status' => 'non_aktif']));
        $response->assertOk();
        $users = $response->viewData('users');
        $this->assertFalse($users->contains('username', 'marketing1'));
    }

    public function test_admin_can_create_user_with_photo(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('profile.jpg', 100, 100);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'nama_lengkap' => 'Baru Sukses',
                'username' => 'barusukses',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'email' => 'baru@example.com',
                'no_hp' => '081234123412',
                'role' => 'marketing',
                'status' => 'aktif',
                'foto_profil' => $file,
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'barusukses',
            'nama_lengkap' => 'Baru Sukses',
        ]);

        $user = User::where('username', 'barusukses')->first();
        $this->assertNotNull($user->foto_profil);
        Storage::disk('public')->assertExists($user->foto_profil);
    }

    public function test_admin_can_update_user_without_password(): void
    {
        $user = User::where('username', 'marketing2')->first();

        $response = $this->actingAs($this->admin)
            ->put(route('admin.users.update', $user->id), [
                'nama_lengkap' => 'Updated Marketing',
                'username' => 'marketing2',
                'email' => 'updated@example.com',
                'role' => 'marketing',
                'status' => 'aktif',
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'nama_lengkap' => 'Updated Marketing',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_admin_cannot_demote_themselves(): void
    {
        $response = $this->actingAs($this->admin)
            ->from(route('admin.users.edit', $this->admin->id))
            ->put(route('admin.users.update', $this->admin->id), [
                'nama_lengkap' => $this->admin->nama_lengkap,
                'username' => $this->admin->username,
                'role' => 'marketing',
                'status' => 'aktif',
            ]);

        $response->assertRedirect(route('admin.users.edit', $this->admin->id));
        $response->assertSessionHas('error', 'Anda tidak dapat mengubah role Anda sendiri.');
        $this->assertEquals(Role::Admin, $this->admin->fresh()->role);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $this->admin->id));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_user_deactivation_instead_of_deletion_when_having_relations(): void
    {
        $marketing = User::where('role', Role::Marketing)->first();

        \Illuminate\Support\Facades\DB::table('prospek')->insert([
            'id_marketing' => $marketing->id,
            'nama_prospek' => 'Calon Konsumen',
            'no_hp' => '0899999999',
            'sumber_prospek' => 'instagram',
            'status_prospek' => 'baru',
            'tanggal_prospek' => now()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $marketing->id));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('warning', 'Pengguna memiliki data transaksi terkait. Status diubah menjadi Non-Aktif.');
        $this->assertEquals('non_aktif', $marketing->fresh()->status);
        $this->assertDatabaseHas('users', ['id' => $marketing->id]);
    }
}
