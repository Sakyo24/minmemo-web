<?php

declare(strict_types=1);

namespace Tests\Unit\ApplicationServices;

use App\ApplicationServices\TodoApplicationServiceInterface;
use App\Models\Todo;
use App\Models\User;
use App\Models\Group;
use App\Repositories\TodoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
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
        $user = User::factory()->make();
        $group = Group::factory()->make([
            'owner_user_id' => $user->id
        ]);
        $expected_todos = Todo::factory(2)
            ->make([
                'user_id' => $user->id,
                'group_id' => $group->id,
            ])
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

        for ($i = 0; $i < count($todos); $i++) {
            $this->assertSame($expected_todos[$i]->id, $todos[$i]->id);
            $this->assertSame($expected_todos[$i]->title, $todos[$i]->title);
            $this->assertSame($expected_todos[$i]->detail, $todos[$i]->detail);
            $this->assertSame($expected_todos[$i]->user_id, $todos[$i]->user_id);
            $this->assertSame($expected_todos[$i]->group_id, $todos[$i]->group_id);
            $this->assertSame((string)$expected_todos[$i]->created_at, (string)$todos[$i]->created_at);
            $this->assertSame((string)$expected_todos[$i]->updated_at, (string)$todos[$i]->updated_at);
        }
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        // データ
        $expected_title = "タイトル";
        $expected_detail = "詳細です。";
        $expected_user_id = 1;
        $expected_group_id = 1;

        $expected_todo = Todo::factory()->make([
            'title' => $expected_title,
            'detail' => $expected_detail,
            'user_id' => $expected_user_id,
            'group_id' => $expected_group_id,
        ]);

        // モックの設定
        $this->todo_repository_mock->shouldReceive('create')
            ->once()
            ->with([
                'title' => $expected_title,
                'detail' => $expected_detail,
                'user_id' => $expected_user_id,
                'group_id' => $expected_group_id,
            ])
            ->andReturn($expected_todo);

        $this->app->instance(TodoRepositoryInterface::class, $this->todo_repository_mock);

        // インスタンス生成
        $this->todo_application_service = $this->app->make(TodoApplicationServiceInterface::class);

        // 実行
        $actual = $this->todo_application_service->create([
            'title' => $expected_title,
            'detail' => $expected_detail,
            'user_id' => $expected_user_id,
            'group_id' => $expected_group_id,
        ]);

        // 検証
        $this->assertNull($actual);
    }
}
