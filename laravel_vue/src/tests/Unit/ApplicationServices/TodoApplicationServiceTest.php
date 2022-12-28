<?php

declare(strict_types=1);

namespace Tests\Unit\ApplicationServices;

use App\ApplicationServices\TodoApplicationServiceInterface;
use App\Models\Todo;
use App\Repositories\TodoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class TodoApplicationServiceTest extends TestCase
{
    /**
     * @var TodoRepositoryInterface $todo_repository_mock
     */
    private TodoRepositoryInterface $todo_repository_mock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->todo_repository_mock = Mockery::mock(TodoRepositoryInterface::class);
    }

    /**
     * @return void
     */
    public function testGetAll(): void
    {
        // データ
        $expected_todos = Todo::factory(2)
            ->make()
            ->sortByDesc('id', SORT_NUMERIC)
            ->values();

        // モックの設定
        $this->todo_repository_mock->shouldReceive('findAll')
            ->once()
            ->andReturn($expected_todos);

        $this->app->instance(TodoRepositoryInterface::class, $this->todo_repository_mock);

        // インスタンス生成
        $this->todo_application_service = $this->app->make(TodoApplicationServiceInterface::class);

        // 実行
        $todos = $this->todo_application_service->getAll();

        // 検証
        $this->assertInstanceOf(Collection::class, $todos);
        $this->assertCount(2, $todos);

        $this->assertSame($expected_todos[0]->id, $todos[0]->id);
        $this->assertSame($expected_todos[0]->title, $todos[0]->title);
        $this->assertSame($expected_todos[0]->detail, $todos[0]->detail);
        $this->assertSame((string)$expected_todos[0]->created_at, (string)$todos[0]->created_at);
        $this->assertSame((string)$expected_todos[0]->updated_at, (string)$todos[0]->updated_at);

        $this->assertSame($expected_todos[1]->id, $todos[1]->id);
        $this->assertSame($expected_todos[1]->title, $todos[1]->title);
        $this->assertSame($expected_todos[1]->detail, $todos[1]->detail);
        $this->assertSame((string)$expected_todos[1]->created_at, (string)$todos[1]->created_at);
        $this->assertSame((string)$expected_todos[1]->updated_at, (string)$todos[1]->updated_at);
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        // データ
        $expected_title = "タイトル";
        $expected_detail = "詳細です。";

        $expected_todo = Todo::factory()->make([
            'title' => $expected_title,
            'detail' => $expected_detail,
        ]);

        // モックの設定
        $this->todo_repository_mock->shouldReceive('create')
            ->once()
            ->with([
                'title' => $expected_title,
                'detail' => $expected_detail,
            ])
            ->andReturn($expected_todo);

        $this->app->instance(TodoRepositoryInterface::class, $this->todo_repository_mock);

        // インスタンス生成
        $this->todo_application_service = $this->app->make(TodoApplicationServiceInterface::class);

        // 実行
        $actual = $this->todo_application_service->create([
            'title' => $expected_title,
            'detail' => $expected_detail,
        ]);

        // 検証
        $this->assertNull($actual);
    }

    /**
     * @return void
     */
    public function testCreateRuntimeException(): void
    {
        // データ
        $expected_title = "タイトル";
        $expected_detail = "詳細です。";

        // モックの設定
        $this->todo_repository_mock->shouldReceive('create')
            ->once()
            ->with([
                'title' => $expected_title,
                'detail' => $expected_detail,
            ])
            ->andThrow(RuntimeException::class);

        $this->app->instance(TodoRepositoryInterface::class, $this->todo_repository_mock);

        // インスタンス生成
        $this->todo_application_service = $this->app->make(TodoApplicationServiceInterface::class);

        // 検証
        $this->expectException(RuntimeException::class);

        // 実行
        $this->todo_application_service->create([
            'title' => $expected_title,
            'detail' => $expected_detail,
        ]);
    }
}
