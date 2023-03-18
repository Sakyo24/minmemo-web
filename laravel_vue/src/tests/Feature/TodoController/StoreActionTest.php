<?php

declare(strict_types=1);

namespace Tests\Feature\TodoController;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreActionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User $user
     */
    private User $user;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * @return void
     */
    public function testStore(): void
    {
        // データ
        $expected_id = 1;
        $expected_title = Str::random();
        $expected_detail = Str::random();

        // リクエスト
        $response = $this->actingAs($this->user)->postJson('/api/todos', [
            'title' => $expected_title,
            'detail' => $expected_detail,
            'user_id' => $this->user->id, // TODO: Web/モバイルで取得方法変えれるか検討
        ]);

        // データ取得
        $todo = Todo::find($expected_id);

        // 検証
        $response->assertCreated();

        $this->assertSame($expected_id, $todo->id);
        $this->assertSame($expected_title, $todo->title);
        $this->assertSame($expected_detail, $todo->detail);
    }

    // TODO: バリデーション実装後失敗パターンをテスト
}