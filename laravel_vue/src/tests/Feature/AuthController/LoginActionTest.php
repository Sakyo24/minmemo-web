<?php

declare(strict_types=1);

namespace Tests\Feature\AuthController;

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
        $expected_id = $this->user->id;
        $expected_name = $this->user->name;
        $expected_email = $this->user->email;
        $expected_password = 'password';

        // リクエスト
        $response = $this->postJson('/login', [
            'email' => $expected_email,
            'password' => $expected_password,
        ]);

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
                    'deleted_at',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertExactJson([
                'user' => [
                    'id' => $expected_id,
                    'name' => $expected_name,
                    'email' => $expected_email,
                    'email_verified_at' => (string)$this->user->email_verified_at,
                    'deleted_at' => null,
                    'created_at' => (string)$this->user->created_at,
                    'updated_at' => (string)$this->user->updated_at,
                ],
            ]);

        $this->assertSame($expected_id, $user->id);
        $this->assertSame($expected_name, $user->name);
        $this->assertSame($expected_email, $user->email);

        $this->assertAuthenticatedAs($this->user);
        $this->assertGuest('admin');
    }

    /**
     * @return void
     */
    public function testLoginByAdmin(): void
    {
        // データ
        $expected_email = $this->admin->email;
        $expected_password = 'password';

        // リクエスト
        $response = $this->postJson('/login', [
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

        $this->assertGuest();
        $this->assertGuest('admin');
    }

    /**
     * @return void
     */
    public function testAllRequiredErrors(): void
    {
        // リクエスト
        $response = $this->postJson('/login', []);

        // 検証
        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email',
                'password',
            ]);

        $this->assertGuest();
        $this->assertGuest('admin');
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
        $response = $this->postJson('/login', [
            'email' => $expected_email,
            'password' => $expected_password,
        ]);

        // 検証
        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email',
            ]);

        $this->assertGuest();
        $this->assertGuest('admin');
    }
}
