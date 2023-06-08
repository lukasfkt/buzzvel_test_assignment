<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AuthTest extends TestCase
{

    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        // seed the database
        $this->artisan('db:seed');
    }

    public function testAcessWithNoToken(): void
    {
        # Trying to access protected routes
        $response = $this->get('/api/tasks');
        $response->assertStatus(401);
    }

    public function testRegister(): void
    {
        $response = $this->post('/api/register', [
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => '123'
        ]);
        $response->assertStatus(201);
    }

    public function testLogin(): void
    {
        User::create([
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => '123'
        ]);
        $response = $this->post('/api/login', [
            'email' => 'test@test.com',
            'password' => '123'
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token',
            ]
        ]);
    }
}
