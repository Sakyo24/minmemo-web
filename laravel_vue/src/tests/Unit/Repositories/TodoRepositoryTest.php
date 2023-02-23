<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Models\Todo;
use App\Repositories\TodoRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var TodoRepository $todo_repository
     */
    private TodoRepository $todo_repository;

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

        User::factory()->create();

        $this->todo_repository = new TodoRepository();

        $this->todos = Todo::factory(2)
            ->create()
            ->sortByDesc('id', SORT_NUMERIC)
            ->values();
    }

    /**
     * @return void
     */
    public function testfindAll(): void
    {
        $todos = $this->todo_repository->findAll();

        $this->assertInstanceOf(Collection::class, $todos);
        $this->assertCount(2, $todos);

        for ($i = 0; $i < count($todos); $i++) {
            $this->assertSame($this->todos[$i]->id, $todos[$i]->id);
            $this->assertSame($this->todos[$i]->title, $todos[$i]->title);
            $this->assertSame($this->todos[$i]->detail, $todos[$i]->detail);
            $this->assertSame($this->todos[$i]->user_id, $todos[$i]->user_id);
            $this->assertSame((string)$this->todos[$i]->created_at, (string)$todos[$i]->created_at);
            $this->assertSame((string)$this->todos[$i]->updated_at, (string)$todos[$i]->updated_at);
        }
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $expected_title = 'タイトル';
        $expected_detail = '詳細です。';
        $expected_user_id = 1;

        $todo = $this->todo_repository->create([
            'title' => $expected_title,
            'detail' => $expected_detail,
            'user_id' => $expected_user_id,
        ]);

        $this->assertInstanceOf(Todo::class, $todo);
        $this->assertSame($expected_title, $todo->title);
        $this->assertSame($expected_detail, $todo->detail);
        $this->assertSame($expected_user_id, $todo->user_id);
    }
}
