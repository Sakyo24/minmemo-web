<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\AuthController;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class LoginActionTest extends TestCase
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

        $this->admin = Admin::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
    }

    /**
     * @return void
     */
    public function testLogin(): void
    {
        // データ
        $expected_id = $this->admin->id;
        $expected_name = $this->admin->name;
        $expected_email = $this->admin->email;
        $expected_password = 'password';

        // リクエスト
        $response = $this->postJson('/admin/login', [
            'email' => $expected_email,
            'password' => $expected_password,
        ]);

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

        $this->assertAuthenticatedAs($this->admin, 'admin');
        $this->assertGuest();
    }

    /**
     * @return void
     */
    public function testLoginByUser(): void
    {
        // データ
        $expected_email = $this->user->email;
        $expected_password = 'password';

        // リクエスト
        $response = $this->postJson('/admin/login', [
            'email' => $expected_email,
            'password' => $expected_password,
        ]);

        // 検証
        $response->assertUnauthorized()
            ->assertJsonStructure([
                'message'
            ])
            ->assertExactJson([
                'message' => 'Unauthenticated.'
            ]);

        $this->assertGuest('admin');
        $this->assertGuest();
    }

    /**
     * @return void
     */
    public function testAllRequiredErrors(): void
    {
        // リクエスト
        $response = $this->postJson('/admin/login', []);

        // 検証
        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email',
                'password',
            ]);

        $this->assertGuest('admin');
        $this->assertGuest();
    }

    /**
     * @return void
     */
    public function testEmailEmailFilterError(): void
    {
        // データ
        $expected_email = Str::random();
        $expected_password = 'password';

        // リクエスト
        $response = $this->postJson('/admin/login', [
            'email' => $expected_email,
            'password' => $expected_password,
        ]);

        // 検証
        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email',
            ]);

        $this->assertGuest('admin');
        $this->assertGuest();
    }
}
