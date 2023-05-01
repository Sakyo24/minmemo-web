<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\AuthController;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetLoginAdminActionTest extends TestCase
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
    public function testGetLoginAdmin(): void
    {
        // データ
        $expected_id = $this->admin->id;
        $expected_name = $this->admin->name;
        $expected_email = $this->admin->email;

        // リクエスト
        $response = $this->actingAs($this->admin, 'admin')->getJson('/api/admin');

        // データ取得
        $admin = Admin::where('email', $expected_email)->first();

        // 検証
        $response->assertOk()
            ->assertJsonStructure([
                'admin' => [
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
                'admin' => [
                    'id' => $expected_id,
                    'name' => $expected_name,
                    'email' => $expected_email,
                    'email_verified_at' => (string)$this->admin->email_verified_at,
                    'created_at' => (string)$this->admin->created_at,
                    'updated_at' => (string)$this->admin->updated_at,
                    'deleted_at' => null,
                ],
            ]);

        $this->assertSame($expected_id, $admin->id);
        $this->assertSame($expected_name, $admin->name);
        $this->assertSame($expected_email, $admin->email);
    }

    /**
     * @return void
     */
    public function testGetLoginAdminByLoginUser(): void
    {
        // リクエスト
        $response = $this->actingAs($this->user)->getJson('/api/admin');

        // 検証
        $response->assertNoContent();
    }

    /**
     * @return void
     */
    public function testGetLogoutAdmin(): void
    {
        // リクエスト
        $response = $this->getJson('/api/admin');

        // 検証
        $response->assertNoContent();
    }
}
