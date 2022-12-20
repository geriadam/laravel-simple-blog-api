<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Post;
use Tests\TestCase;

class PostTest extends TestCase
{
    use DatabaseTransactions;

    protected function authenticateWriter()
    {
        $user = User::create([
            'name' => 'writer',
            'email' => rand(12345, 678910) . 'test@mail.com',
            'password' => Hash::make(User::DEFAULT_PASS),
        ]);
        $user->assignRole(User::USER_ROLE_WRITER);

        if (!auth()->attempt(['email' => $user->email, 'password' => User::DEFAULT_PASS])) {
            return response(['message' => 'Login credentials are invaild']);
        }

        return ["user" => $user, "token" => $user->createToken('Personal Access Token')->accessToken];
    }

    protected function authenticateManager()
    {
        $user = User::create([
            'name' => 'manager',
            'email' => rand(12345, 678910) . 'test@mail.com',
            'password' => Hash::make(User::DEFAULT_PASS),
        ]);
        $user->assignRole(User::USER_ROLE_MANAGER);

        if (!auth()->attempt(['email' => $user->email, 'password' => User::DEFAULT_PASS])) {
            return response(['message' => 'Login credentials are invaild']);
        }

        return ["user" => $user, "token" => $user->createToken('Personal Access Token')->accessToken];
    }

    protected function authenticateAdmin()
    {
        $user = User::create([
            'name' => 'admin',
            'email' => rand(12345, 678910) . 'test@mail.com',
            'password' => Hash::make(User::DEFAULT_PASS),
        ]);
        $user->assignRole(User::USER_ROLE_ADMIN);

        if (!auth()->attempt(['email' => $user->email, 'password' => User::DEFAULT_PASS])) {
            return response(['message' => 'Login credentials are invaild']);
        }

        return ["user" => $user, "token" => $user->createToken('Personal Access Token')->accessToken];
    }

    public function testAddPostAsWriterUser()
    {
        $token = $this->authenticateWriter()["token"];

        // Arrange
        $data = [
            'title' => 'Test Post',
            'author_id' => 1,
            'content' => 'This is a test post.',
            'slug' => 'test-post',
            'status' => '1'
        ];

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST', '/api/posts', $data);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('posts', $data);
    }

    public function testUpdatePostAsWriterUser()
    {
        $auth = $this->authenticateWriter();
        $user = $auth['user'];
        $token = $auth['token'];

        // Arrange
        $post = Post::factory()->create([
            'title' => 'Test Post',
            'content' => 'This is an test post.',
            'slug' => 'test-post',
            'status' => '1',
            "author_id" => $user->id
        ]);
        $data = [
            'title' => 'Updated Test Post',
            'content' => 'This is an updated test post.',
            'slug' => 'updated-test-post',
            'status' => '1',
            "author_id" => $user->id
        ];

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('PUT', '/api/posts/' . $post->id, $data);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', $data);
    }

    public function testDeletePostAsWriterUser()
    {
        $auth = $this->authenticateWriter();
        $user = $auth['user'];
        $token = $auth['token'];

        // Arrange
        $post = Post::factory()->create([
            'title' => 'Test Post',
            'content' => 'This is an test post.',
            'slug' => 'test-post',
            'status' => '1',
            "author_id" => $user->id
        ]);

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('DELETE', '/api/posts/' . $post->id);

        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function testUpdatePostAsOtherWriterUser()
    {
        $auth1 = $this->authenticateWriter();
        $user1 = $auth1['user'];
        $token1 = $auth1['token'];

        $auth2 = $this->authenticateWriter();
        $user2 = $auth2['user'];
        $token2 = $auth2['token'];

        // Arrange
        $post = Post::factory()->create([
            'title' => 'Test Post',
            'content' => 'This is an test post.',
            'slug' => 'test-post',
            'status' => '1',
            "author_id" => $user1->id
        ]);
        $data = [
            'title' => 'Updated Test Post',
            'content' => 'This is an updated test post.',
            'slug' => 'updated-test-post',
            'status' => '1',
            "author_id" => $user2->id
        ];

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token2,
        ])->json('PUT', '/api/posts/' . $post->id, $data);

        // Assert
        $response->assertStatus(401);
    }
}
