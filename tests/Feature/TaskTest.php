<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Users can view their tasks.
     */
    public function test_api_tasks_index(): void
    {
        $user = User::factory()->create();

        Task::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/tasks');

        $response->assertStatus(200)->assertJsonCount(3);
    }

    /**
     * Users can create new tasks.
     */
    public function test_api_tasks_create(): void
    {
        $user = User::factory()->create();

        $taskData = [
            'title' => 'New Task',
            'description' => 'Task description',
            'time_estimate' => 60,
        ];

        $response = $this->actingAs($user)->postJson('/api/tasks', $taskData);

        $response->assertStatus(201);
    }

    /**
     * Users can update their tasks.
     */
    public function test_api_tasks_update(): void
    {
        $user = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user->id]);

        $updatedData = [
            'title' => 'Updated Task Title',
            'description' => 'Updated description',
            'time_estimate' => 90,
        ];

        $response = $this->actingAs($user)->putJson("/api/tasks/{$task->id}", $updatedData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
            'description' => 'Updated description',
            'time_estimate' => 90,
        ]);
    }

    /**
     * Users can delete their tasks.
     */
    public function test_api_tasks_delete(): void
    {
        $user = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /**
     * Users cannot access tasks of other users.
     */
    public function test_api_tasks_access_control(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }
}
