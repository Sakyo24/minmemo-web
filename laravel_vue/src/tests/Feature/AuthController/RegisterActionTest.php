<?php

declare(strict_types=1);

namespace Tests\AuthController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegisterActionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        // データ
        $expected_id = 1;
        $expected_name = 'テストユーザー';
        $expected_email = 'test@example.com';
        $expected_password = 'password';

        // リクエスト
        $response = $this->postJson('/register', [
            'name' => $expected_name,
            'email' => $expected_email,
            'password' => $expected_password,
            'password_confirmation' => $expected_password,
        ]);

        // データ取得
        $user = User::where('email', $expected_email)->first();

        // 検証
        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertExactJson([
                'user' => [
                    'id' => $expected_id,
                    'name' => $expected_name,
                    'email' => $expected_email,
                    'created_at' => (string)$user->created_at,
                    'updated_at' => (string)$user->updated_at,
                ],
            ]);

        $this->assertSame($expected_id, $user->id);
        $this->assertSame($expected_name, $user->name);
        $this->assertSame($expected_email, $user->email);

        $this->assertGuest();
    }

    /**
     * @return void
     */
    public function testAllRequiredErrors(): void
    {
        // リクエスト
        $response = $this->postJson('/register', []);

        // データ取得
        $user = User::all();

        // 検証
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email',
                'password',
                'password_confirmation',
            ]);

        $this->assertCount(0, $user);

        $this->assertGuest();
    }

    /**
     * @return void
     */
    public function testNameMaxError(): void
    {
        // データ
        $expected_name = Str::random(256);
        $expected_email = 'test@exmaple.com';
        $expected_password = 'password';

        // リクエスト
        $response = $this->postJson('/register', [
            'name' => $expected_name,
            'email' => $expected_email,
            'password' => $expected_password,
            'password_confirmation' => $expected_password,
        ]);

        // データ取得
        $user = User::all();

        // 検証
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
            ]);

        $this->assertCount(0, $user);

        $this->assertGuest();
    }

    /**
     * @return void
     */
    public function testEmailUniqueError(): void
    {
        // データ
        $expected_name = 'テストユーザー';
        $expected_email = 'test@example.com';
        $expected_password = 'password';

        // DB保存
        User::factory()->create([
            'email' => $expected_email,
        ]);

        // リクエスト
        $response = $this->postJson('/register', [
            'name' => $expected_name,
            'email' => $expected_email,
            'password' => $expected_password,
            'password_confirmation' => $expected_password,
        ]);

        // データ取得
        $user = User::all();

        // 検証
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
            ]);

        $this->assertCount(1, $user);

        $this->assertGuest();
    }

    /**
     * @return void
     */
    public function testEmailMaxError(): void
    {
        // データ
        $expected_name = 'テストユーザー';
        $expected_email = Str::random(256);
        $expected_password = 'password';

        // リクエスト
        $response = $this->postJson('/register', [
            'name' => $expected_name,
            'email' => $expected_email,
            'password' => $expected_password,
            'password_confirmation' => $expected_password,
        ]);

        // データ取得
        $user = User::all();

        // 検証
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
            ]);

        $this->assertCount(0, $user);

        $this->assertGuest();
    }

    /**
     * @return void
     */
    public function testEmailEmailFilterError(): void
    {
        // データ
        $expected_name = 'テストユーザー';
        $expected_email = Str::random(255);
        $expected_password = 'password';

        // リクエスト
        $response = $this->postJson('/register', [
            'name' => $expected_name,
            'email' => $expected_email,
            'password' => $expected_password,
            'password_confirmation' => $expected_password,
        ]);

        // データ取得
        $user = User::all();

        // 検証
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
            ]);

        $this->assertCount(0, $user);

        $this->assertGuest();
    }

    /**
     * @return void
     */
    public function testPasswordConfirmedError(): void
    {
        // データ
        $expected_name = 'テストユーザー';
        $expected_email = 'test@example.com';
        $expected_password = 'password';
        $expected_password_confirmation = 'password1';

        // リクエスト
        $response = $this->postJson('/register', [
            'name' => $expected_name,
            'email' => $expected_email,
            'password' => $expected_password,
            'password_confirmation' => $expected_password_confirmation,
        ]);

        // データ取得
        $user = User::all();

        // 検証
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'password',
            ]);

        $this->assertCount(0, $user);

        $this->assertGuest();
    }

    /**
     * @return void
     */
    public function testPasswordBetweenMinError(): void
    {
        // データ
        $expected_name = 'テストユーザー';
        $expected_email = 'test@example.com';
        $expected_password = Str::random(7);

        // リクエスト
        $response = $this->postJson('/register', [
            'name' => $expected_name,
            'email' => $expected_email,
            'password' => $expected_password,
            'password_confirmation' => $expected_password,
        ]);

        // データ取得
        $user = User::all();

        // 検証
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'password',
            ]);

        $this->assertCount(0, $user);

        $this->assertGuest();
    }

    /**
     * @return void
     */
    public function testPasswordBetweenMaxError(): void
    {
        // データ
        $expected_name = 'テストユーザー';
        $expected_email = 'test@example.com';
        $expected_password = Str::random(17);

        // リクエスト
        $response = $this->postJson('/register', [
            'name' => $expected_name,
            'email' => $expected_email,
            'password' => $expected_password,
            'password_confirmation' => $expected_password,
        ]);

        // データ取得
        $user = User::all();

        // 検証
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'password',
            ]);

        $this->assertCount(0, $user);

        $this->assertGuest();
    }

    /**
     * @return void
     */
    public function testPasswordAlphaNumError(): void
    {
        // データ
        $expected_name = 'テストユーザー';
        $expected_email = 'test@example.com';
        $expected_password = 'ああああああああ';

        // リクエスト
        $response = $this->postJson('/register', [
            'name' => $expected_name,
            'email' => $expected_email,
            'password' => $expected_password,
            'password_confirmation' => $expected_password,
        ]);

        // データ取得
        $user = User::all();

        // 検証
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'password',
            ]);

        $this->assertCount(0, $user);

        $this->assertGuest();
    }
}
