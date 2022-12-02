<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

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

        $this->assertSame($this->todos[0]->id, $todos[0]->id);
        $this->assertSame($this->todos[0]->title, $todos[0]->title);
        $this->assertSame($this->todos[0]->detail, $todos[0]->detail);
        $this->assertSame((string)$this->todos[0]->created_at, (string)$todos[0]->created_at);
        $this->assertSame((string)$this->todos[0]->updated_at, (string)$todos[0]->updated_at);

        $this->assertSame($this->todos[1]->id, $todos[1]->id);
        $this->assertSame($this->todos[1]->title, $todos[1]->title);
        $this->assertSame($this->todos[1]->detail, $todos[1]->detail);
        $this->assertSame((string)$this->todos[1]->created_at, (string)$todos[1]->created_at);
        $this->assertSame((string)$this->todos[1]->updated_at, (string)$todos[1]->updated_at);
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $expected_title = 'タイトル';
        $expected_detail = '詳細です。';

        $todo = $this->todo_repository->create([
            'title' => $expected_title,
            'detail' => $expected_detail,
        ]);

        $this->assertInstanceOf(Todo::class, $todo);
        $this->assertSame($expected_title, $todo->title);
        $this->assertSame($expected_detail, $todo->detail);
    }
}
