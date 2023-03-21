<?php

declare(strict_types=1);

namespace Tests\Feature\TodoController;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexActionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User $user
     */
    private User $user;

    /**
     * @var Collection $todos
     */
    private Collection $todos;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->todos = Todo::factory(1)->create([
            'user_id' => $this->user->id,
            'group_id' => null,
        ]);
    }

    /**
     * @return void
     */
    public function testGetTodos(): void
    {
        // リクエスト
        $response = $this->actingAs($this->user)->getJson('/api/todos');

        // 検証
        $response->assertOk()
            ->assertJsonStructure([
                'todos' => [
                    '*' => [
                        'id',
                        'title',
                        'detail',
                        'user_id',
                        'group_id',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                    ]
                ],
            ])
            ->assertExactJson([
                'todos' => [
                    [
                        'id' => $this->todos[0]->id,
                        'title' => $this->todos[0]->title,
                        'detail' => $this->todos[0]->detail,
                        'user_id' => $this->user->id,
                        'group_id' => null,
                        'created_at' => (string)$this->todos[0]->created_at,
                        'updated_at' => (string)$this->todos[0]->updated_at,
                        'deleted_at' => null,
                    ]
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testUnauthorizedAccess(): void
    {
        // リクエスト
        $response = $this->getJson('/api/todos');

        // 検証
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
