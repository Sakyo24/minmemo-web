<?php

declare(strict_types=1);

namespace Tests\Feature\TodoController;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyActionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User $user
     */
    private User $user;

    /**
     * @var Todo $todo
     */
    private Todo $todo;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->todo = Todo::factory()->create([
            'user_id' => $this->user->id,
            'group_id' => null,
        ]);
    }

    /**
     * @return void
     */
    public function testDestroy(): void
    {
        // データ
        $expected_id = $this->todo->id;

        // リクエスト
        $response = $this->actingAs($this->user)->deleteJson("/api/todos/$expected_id");

        // データ取得
        $todo = Todo::find($expected_id);

        // 検証
        $response->assertNoContent();

        $this->assertNull($todo);
    }

    // TODO: 仕様修正後、各パターンの削除テスト
}
