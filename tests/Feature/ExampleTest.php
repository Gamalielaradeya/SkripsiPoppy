<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')
            ->assertRedirect('/login');
    }

    public function test_admin_can_login_and_open_dashboard(): void
    {
        User::query()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Belum ada data monitoring real-device');
    }

    public function test_placeholder_routes_are_protected_and_available_to_admin(): void
    {
        $user = User::query()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $routes = [
            '/devices',
            '/devices/1',
            '/accurate-audit',
            '/accurate-audit/1',
            '/incidents',
            '/incidents/1',
            '/alerts',
            '/alerts/1',
            '/remote-actions',
            '/remote-actions/1',
            '/advanced-logs',
            '/advanced-logs/1',
            '/settings',
        ];

        foreach ($routes as $uri) {
            $this->actingAs($user)->get($uri)->assertOk();
        }
    }
}
