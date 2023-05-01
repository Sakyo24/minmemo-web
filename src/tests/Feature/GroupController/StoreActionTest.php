<?php

declare(strict_types=1);

namespace Tests\Feature\GroupController;

use App\Models\Group;
use App\Models\GroupUser;
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
        $expected_name = Str::random();

        // リクエスト
        $response = $this->actingAs($this->user)->postJson('/api/groups', [
            'name' => $expected_name,
        ]);

        // データ取得
        $group = Group::first();
        $group_user = GroupUser::first();

        // 検証
        $response->assertCreated();

        $this->assertSame($expected_name, $group->name);
        $this->assertSame($group_user->group_id, $group->id);
        $this->assertSame($group_user->user_id, $this->user->id);
    }

    /**
     * @return void
     */
    public function testUnauthorizedAccess(): void
    {
        // データ
        $expected_name = Str::random();

        // リクエスト
        $response = $this->postJson('/api/groups', [
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
    public function testNameRequiredErrors(): void
    {
        // リクエスト
        $response = $this->actingAs($this->user)->postJson('/api/groups', []);

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
        $expected_name = Str::random(26);
        
        // リクエスト
        $response = $this->actingAs($this->user)->postJson('/api/groups', [
            'name' => $expected_name,
        ]);

        // 検証
        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
            ]);
    }
}
