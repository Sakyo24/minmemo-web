<?php

declare(strict_types=1);

namespace Tests\Feature\GroupController;

use App\Models\Group;
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
     * @var Group $group
     */
    private Group $group;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->group = Group::factory()->create([
            'owner_user_id' => $this->user->id,
        ]);
    }

    /**
     * @return void
     */
    public function testUpdate(): void
    {
        // データ
        $expected_id = $this->group->id;
        $expected_name = Str::random();

        // リクエスト
        $response = $this->actingAs($this->user)->putJson("/api/groups/$expected_id", [
            'name' => $expected_name,
        ]);

        // データ取得
        $group = Group::find($expected_id);

        // 検証
        $response->assertOk()
            ->assertJsonStructure([
                'group' => [
                    'id',
                    'name',
                    'owner_user_id',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ])
            ->assertExactJson([
                'group' => [
                    'id' => $expected_id,
                    'name' => $expected_name,
                    'owner_user_id' => $this->user->id,
                    'created_at' => (string)$this->group->created_at,
                    'updated_at' => (string)$this->group->updated_at,
                    'deleted_at' => null,
                ],
            ]);

        $this->assertSame($expected_id, $group->id);
        $this->assertSame($expected_name, $group->name);
    }

    /**
     * @return void
     */
    public function testUnauthorizedAccess(): void
    {
        // データ
        $expected_id = $this->group->id;
        $expected_name = Str::random();

        // リクエスト
        $response = $this->putJson("/api/groups/$expected_id", [
            'name' => $expected_name,
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
    public function testNameRequiredError(): void
    {
        // データ
        $expected_id = $this->group->id;

        // リクエスト
        $response = $this->actingAs($this->user)->putJson("/api/groups/$expected_id", []);

        // 検証
        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    /**
     * @return void
     */
    public function testNameMaxError(): void
    {
        // データ
        $expected_id = $this->group->id;
        $expected_name = Str::random(26);

        // リクエスト
        $response = $this->actingAs($this->user)->putJson("/api/groups/$expected_id", [
            'name' => $expected_name,
        ]);

        // 検証
        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
            ]);
    }
}
