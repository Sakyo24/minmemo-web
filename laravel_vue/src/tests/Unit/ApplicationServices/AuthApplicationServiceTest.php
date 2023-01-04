<?php

declare(strict_types=1);

namespace Tests\Unit\ApplicationServices;

use App\ApplicationServices\AuthApplicationServiceInterface;
use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class AuthApplicationServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var UserRepositoryInterface $user_repository_mock
     */
    private UserRepositoryInterface $user_repository_mock;

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

        $this->user_repository_mock = Mockery::mock(UserRepositoryInterface::class);

        // TODO: DBに依存しているテスト
        $this->user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        // データ
        $expected_name = 'ユーザー名';
        $expected_email = 'test@exmaple.com';
        $expected_password = 'password';
        $expected_user = User::factory()->make([
            'name' => $expected_name,
            'email' => $expected_email,
            'password' => Hash::make($expected_password),
        ]);

        // モックの設定
        $this->user_repository_mock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($actual) use ($expected_name, $expected_email, $expected_password) {
                $this->assertSame($expected_name, $actual['name']);
                $this->assertSame($expected_email, $actual['email']);
                $this->assertTrue(Hash::check($expected_password, $actual['password']));

                return true;
            }))
            ->andReturn($expected_user);

        $this->app->instance(UserRepositoryInterface::class, $this->user_repository_mock);

        // インスタンス生成
        $auth_application_service = $this->app->make(AuthApplicationServiceInterface::class);

        // 実行
        $user = $auth_application_service->register(
            [
                'name' => $expected_name,
                'email' => $expected_email,
                'password' => $expected_password,
            ]
        );

        // 検証
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($expected_name, $user->name);
        $this->assertSame($expected_email, $user->email);
    }

    /**
     * @return void
     */
    public function testRegisterRuntimeException(): void
    {
        // データ
        $expected_name = 'ユーザー名';
        $expected_email = 'test@exmaple.com';
        $expected_password = 'password';

        // モックの設定
        $this->user_repository_mock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($actual) use ($expected_name, $expected_email, $expected_password) {
                $this->assertSame($expected_name, $actual['name']);
                $this->assertSame($expected_email, $actual['email']);
                $this->assertTrue(Hash::check($expected_password, $actual['password']));

                return true;
            }))
            ->andThrow(RuntimeException::class);

        $this->app->instance(UserRepositoryInterface::class, $this->user_repository_mock);

        // インスタンス生成
        $auth_application_service = $this->app->make(AuthApplicationServiceInterface::class);

        // 検証
        $this->expectException(RuntimeException::class);

        // 実行
        $auth_application_service->register(
            [
                'name' => $expected_name,
                'email' => $expected_email,
                'password' => $expected_password,
            ]
        );
    }

    /**
     * TODO: DBに依存しているテスト
     *
     * @return void
     */
    public function testLogin(): void
    {
        // データ
        $expected_name = $this->user->name;
        $expected_email = $this->user->email;
        $expected_password = 'password';
        $expected_user = $this->user;

        // モックの設定
        $this->user_repository_mock->shouldReceive('findById')
            ->once()
            ->with($expected_user->id)
            ->andReturn($expected_user);

        $this->app->instance(UserRepositoryInterface::class, $this->user_repository_mock);

        // インスタンス生成
        $auth_application_service = $this->app->make(AuthApplicationServiceInterface::class);

        // 実行
        $user = $auth_application_service->login(
            [
                'email' => $expected_email,
                'password' => $expected_password,
            ]
        );

        // 検証
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($expected_name, $user->name);
        $this->assertSame($expected_email, $user->email);
    }

    /**
     * TODO: DBに依存しているテスト
     *
     * @return void
     */
    public function testLoginAuthenticationException(): void
    {
        // データ
        $expected_email = $this->user->email;
        $expected_password = 'password1';

        // インスタンス生成
        $auth_application_service = $this->app->make(AuthApplicationServiceInterface::class);

        // 検証
        $this->expectException(AuthenticationException::class);

        // 実行
        $auth_application_service->login(
            [
                'email' => $expected_email,
                'password' => $expected_password,
            ]
        );
    }

    /**
     * @return void
     */
    public function testLogout(): void
    {
        // インスタンス生成
        $auth_application_service = $this->app->make(AuthApplicationServiceInterface::class);

        // 実行
        $auth_application_service->logout();

        // 検証
        $this->assertTrue(true);
    }
}
