<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseOneAuthAndRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_unauthenticated_user_accessing_root_redirects_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_login_and_is_redirected_to_admin_dashboard(): void
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_marketing_can_login_and_is_redirected_to_marketing_dashboard(): void
    {
        $response = $this->post('/login', [
            'username' => 'marketing1',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('marketing.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_manajemen_can_login_and_is_redirected_to_manajemen_dashboard(): void
    {
        $response = $this->post('/login', [
            'username' => 'manajemen',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('manajemen.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_user_can_logout_and_is_redirected_to_login(): void
    {
        $user = User::where('username', 'admin')->first();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_marketing_user_cannot_access_admin_routes(): void
    {
        $marketing = User::where('username', 'marketing1')->first();

        $response = $this->actingAs($marketing)->get('/admin/dashboard');

        $response->assertRedirect(route('marketing.dashboard'));
    }
}
