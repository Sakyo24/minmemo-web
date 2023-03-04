<?php

declare(strict_types=1);

namespace Tests\Feature\AuthController;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetLoginUserActionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Admin $admin
     */
    private Admin $admin;

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

        $this->admin = Admin::factory()->create();

        $this->user = User::factory()->create();
    }

    /**
     * @return void
     */
    public function testGetLoginUser(): void
    {
        // データ
        $expected_id = $this->user->id;
        $expected_name = $this->user->name;
        $expected_email = $this->user->email;

        // リクエスト
        $response = $this->actingAs($this->user)->getJson('/api/auth');

        // データ取得
        $user = User::where('email', $expected_email)->first();

        // 検証
        $response->assertOk()
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ])
            ->assertExactJson([
                'user' => [
                    'id' => $expected_id,
                    'name' => $expected_name,
                    'email' => $expected_email,
                    'email_verified_at' => (string)$this->user->email_verified_at,
                    'created_at' => (string)$this->user->created_at,
                    'updated_at' => (string)$this->user->updated_at,
                    'deleted_at' => null,
                ],
            ]);

        $this->assertSame($expected_id, $user->id);
        $this->assertSame($expected_name, $user->name);
        $this->assertSame($expected_email, $user->email);
    }

    /**
     * @return void
     */
    public function testGetLogoutUser(): void
    {
        // リクエスト
        $response = $this->getJson('/api/auth');

        // 検証
        $response->assertNoContent();
    }
}
