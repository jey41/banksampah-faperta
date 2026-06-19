<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'testuser@bsfp.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '08123456789',
            'address' => 'Jl. Faperta No. 1',
            'umur' => 20,
            'gender' => 'L',
            'status_pekerjaan' => 'mahasiswa',
            'universitas' => 'Universitas Faperta',
            'fakultas' => 'Fakultas Pertanian',
            'pendidikan_terakhir' => 'sma',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login', absolute: false));
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@bsfp.com',
            'status' => 'pending',
            'phone' => '08123456789',
            'address' => 'Jl. Faperta No. 1',
            'umur' => 20,
            'gender' => 'L',
            'status_pekerjaan' => 'mahasiswa',
            'universitas' => 'Universitas Faperta',
            'fakultas' => 'Fakultas Pertanian',
            'pendidikan_terakhir' => 'sma',
        ]);
    }
}
