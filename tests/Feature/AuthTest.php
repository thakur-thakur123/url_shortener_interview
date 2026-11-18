<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_superadmin_with_raw_sql(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'superadmin@example.com',
            'role' => 'SuperAdmin',
            'company_id' => null,
        ]);
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(1, User::where('email', 'superadmin@example.com')->count());
    }

    public function test_login_page_is_shown_to_guests(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_superadmin_can_login_and_logout(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->post('/login', [
            'email' => 'superadmin@example.com',
            'password' => 'password',
        ])->assertRedirect('/');

        $this->assertAuthenticated();

        $this->post('/logout');
        $this->assertGuest();
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->from('/login')->post('/login', [
            'email' => 'superadmin@example.com',
            'password' => 'wrong',
        ])->assertRedirect('/login')->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_public_registration_does_not_exist(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(404);

        $this->assertDatabaseMissing('users', ['email' => 'hacker@example.com']);
    }
}
