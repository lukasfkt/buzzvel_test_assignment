<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class TasksTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        // seed the database
        $this->artisan('db:seed');
        User::create([
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => '123'
        ]);
    }

    public function testListAll(): void
    {
        $credentials = [
            'email' => 'test@test.com',
            'password' => '123'
        ];
        $token = auth()->attempt($credentials);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->get('/api/tasks/');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'title',
                'description',
                'file_name',
                'completed',
                'user_id',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function testTaskDetail(): void
    {
        $user = User::where('email', 'test@test.com')->first();
        $task = Task::create([
            'title' => 'Test',
            'description' => 'Test',
            'user_id' => $user->id
        ]);
        $credentials = [
            'email' => 'test@test.com',
            'password' => '123'
        ];
        $token = auth()->attempt($credentials);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->get('/api/tasks/' . $task->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'title',
            'description',
            'file_name',
            'completed',
            'user_id',
            'created_at',
            'updated_at'
        ]);
    }

    public function testTaskCreation(): void
    {
        $credentials = [
            'email' => 'test@test.com',
            'password' => '123'
        ];
        $token = auth()->attempt($credentials);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->post('/api/tasks/', [
            'title' => 'Test',
            'description' => 'Test'
        ]);
        $response->assertStatus(201);
        $response->assertJsonIsObject();
    }


    public function testTaskModification(): void
    {
        $user = User::where('email', 'test@test.com')->first();
        $task = Task::create([
            'title' => 'Test',
            'description' => 'Test',
            'user_id' => $user->id
        ]);
        $credentials = [
            'email' => 'test@test.com',
            'password' => '123'
        ];
        $token = auth()->attempt($credentials);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->put('/api/tasks/' . $task->id, [
            'id' => $task->id,
            'title' => 'Test 2',
            'description' => 'Test 2',
            'completed' => true
        ]);
        $response->assertStatus(200);
    }

    public function testTaskDelete(): void
    {
        $user = User::where('email', 'test@test.com')->first();
        $task = Task::create([
            'title' => 'Test',
            'description' => 'Test',
            'user_id' => $user->id
        ]);
        $credentials = [
            'email' => 'test@test.com',
            'password' => '123'
        ];
        $token = auth()->attempt($credentials);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->delete('/api/tasks/' . $task->id);
        $response->assertStatus(200);
    }
}
