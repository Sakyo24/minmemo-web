<?php

declare(strict_types=1);

namespace Tests\Feature\TodoController;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateActionTest extends TestCase
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
    public function testUpdate(): void
    {
        // データ
        $expected_id = $this->todo->id;
        $expected_title = Str::random();
        $expected_detail = Str::random();

        // リクエスト
        $response = $this->actingAs($this->user)->putJson("/api/todos/$expected_id", [
            'title' => $expected_title,
            'detail' => $expected_detail,
        ]);

        // データ取得
        $todo = Todo::find($expected_id);

        // 検証
        $response->assertOk()
            ->assertJsonStructure([
                'todo' => [
                    'id',
                    'title',
                    'detail',
                    'user_id',
                    'group_id',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ])
            ->assertExactJson([
                'todo' => [
                    'id' => $expected_id,
                    'title' => $expected_title,
                    'detail' => $expected_detail,
                    'user_id' => $this->user->id,
                    'group_id' => null,
                    'created_at' => (string)$this->todo->created_at,
                    'updated_at' => (string)$this->todo->updated_at,
                    'deleted_at' => null,
                ],
            ]);

        $this->assertSame($expected_id, $todo->id);
        $this->assertSame($expected_title, $todo->title);
        $this->assertSame($expected_detail, $todo->detail);
    }

    /**
     * @return void
     */
    public function testUnauthorizedAccess(): void
    {
        // データ
        $expected_id = $this->todo->id;
        $expected_title = Str::random();
        $expected_detail = Str::random();

        // リクエスト
        $response = $this->putJson("/api/todos/$expected_id", [
            'title' => $expected_title,
            'detail' => $expected_detail,
        ]);

        // 検証
        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    /**
     * @return void
     */
    public function testAllRequiredErrors(): void
    {
        // データ
        $expected_id = $this->todo->id;
        $expected_title = '';
        $expected_detail = '';

        // リクエスト
        $response = $this->actingAs($this->user)->putJson("/api/todos/$expected_id", [
            'title' => $expected_title,
            'detail' => $expected_detail,
        ]);

        // 検証
        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'title',
                'detail',
            ]);
    }

    /**
     * @return void
     */
    public function testTitleMaxError(): void
    {
        // データ
        $expected_id = $this->todo->id;
        $expected_title = Str::random(26);
        $expected_detail = Str::random();

        // リクエスト
        $response = $this->actingAs($this->user)->putJson("/api/todos/$expected_id", [
            'title' => $expected_title,
            'detail' => $expected_detail,
        ]);

        // 検証
        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'title',
            ]);
    }

    /**
     * @return void
     */
    public function testDetailMaxError(): void
    {
        // データ
        $expected_id = $this->todo->id;
        $expected_title = Str::random();
        $expected_detail = Str::random(256);

        // リクエスト
        $response = $this->actingAs($this->user)->putJson("/api/todos/$expected_id", [
            'title' => $expected_title,
            'detail' => $expected_detail,
        ]);

        // 検証
        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'detail',
            ]);
    }
}
