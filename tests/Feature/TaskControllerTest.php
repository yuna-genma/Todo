<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Task;
use App\Models\User;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_task_index_can_get(): void
    {
        $user = User::factory()->create();
        Task::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertViewHas('tasks');
    }

    /** @test */
    public function test_task_can_create()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'content' => 'テスト投稿',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'content' => 'テスト投稿',
        ]);
    }

    /** @test */
    public function test_task_can_update()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'content' => '更新後のタスク'
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'content' => '更新後のタスク',
        ]);
    }

    /** @test */
    public function test_task_can_delete()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);

    }

    public function test_task_content_required()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'content' => '',
        ]);

        $response->assertSessionHasErrors('content');
    }

    /** @test */
    public function test_task_content_max_25_characters()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'content' => str_repeat('あ', 25),
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'content' => str_repeat('あ', 25),
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function test_task_content_must_be_within_25()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'content' => str_repeat('あ', 26),
        ]);

        $response->assertSessionHasErrors('content');
    }
}
