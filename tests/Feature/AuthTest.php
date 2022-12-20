<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_register()
    {
        $response = $this->json('POST', '/api/auth/register', [
            'name' =>  'Test',
            'email' => rand(12345, 678910) . 'test@example.com',
            'password' => Hash::make(User::DEFAULT_PASS),
            'role' => User::USER_ROLE_WRITER
        ]);

        $response->assertStatus(201);

        $this->assertArrayHasKey('data', $response->json());
    }

    public function test_login()
    {
        $data = [
            'name' => 'Test',
            'email'=> rand(12345, 678910) . 'test@example.com',
            'password' => Hash::make(User::DEFAULT_PASS)
        ];
        $user = User::create($data);
        $user->assignRole(User::USER_ROLE_WRITER);

        // Simulated landing
        $response = $this->json('POST', '/api/auth/login', [
            'email' => $data['email'],
            'password' => User::DEFAULT_PASS,
        ]);

        // Determine whether the login is successful and receive token
        $response->assertStatus(200);

        $this->assertArrayHasKey('access_token', $response->json()['data']);
    }
}
