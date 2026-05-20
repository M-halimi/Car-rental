<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthSeparationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'customer', 'guard_name' => 'web']);
        Role::create(['name' => 'agency', 'guard_name' => 'web']);
        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
    }

    public function test_customer_role_exists(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        $this->assertTrue($user->hasRole('customer'));
        $this->assertFalse($user->hasRole('agency'));
        $this->assertFalse($user->hasRole('super_admin'));
    }

    public function test_agency_user_has_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole('agency');

        $this->assertTrue($user->hasRole('agency'));
    }

    public function test_admin_user_has_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $this->assertTrue($user->hasRole('super_admin'));
    }

    public function test_customer_can_authenticate_on_web_guard(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('customer');

        $this->assertTrue(
            Auth::attempt([
                'email' => $user->email,
                'password' => 'password',
            ])
        );
    }

    public function test_agency_can_authenticate_on_web_guard(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('agency');

        $this->assertTrue(
            Auth::attempt([
                'email' => $user->email,
                'password' => 'password',
            ])
        );
    }

    public function test_admin_can_authenticate_on_web_guard(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('super_admin');

        $this->assertTrue(
            Auth::attempt([
                'email' => $user->email,
                'password' => 'password',
            ])
        );
    }

    public function test_invalid_credentials_rejected(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('correct_password'),
        ]);
        $user->assignRole('customer');

        $this->assertFalse(
            Auth::attempt([
                'email' => $user->email,
                'password' => 'wrong_password',
            ])
        );
    }

    public function test_customer_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        $adminPanel = app('filament')->getPanel('admin');
        $this->assertFalse($user->canAccessPanel($adminPanel));
    }

    public function test_customer_cannot_access_agency_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        $agencyPanel = app('filament')->getPanel('agency');
        $this->assertFalse($user->canAccessPanel($agencyPanel));
    }

    public function test_agency_can_access_agency_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('agency');

        $agencyPanel = app('filament')->getPanel('agency');
        $this->assertTrue($user->canAccessPanel($agencyPanel));
    }

    public function test_agency_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('agency');

        $adminPanel = app('filament')->getPanel('admin');
        $this->assertFalse($user->canAccessPanel($adminPanel));
    }

    public function test_admin_can_access_admin_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $adminPanel = app('filament')->getPanel('admin');
        $this->assertTrue($user->canAccessPanel($adminPanel));
    }

    public function test_admin_can_access_agency_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $agencyPanel = app('filament')->getPanel('agency');
        $this->assertTrue($user->canAccessPanel($agencyPanel));
    }

    public function test_frontend_login_controller_rejects_non_customer(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('agency');

        $response = $this->post(route('frontend.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_frontend_login_controller_allows_customer(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('customer');

        $response = $this->post(route('frontend.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('frontend.dashboard'));
        $this->assertAuthenticated();
    }
}
