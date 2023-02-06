<?php

declare(strict_types=1);

namespace Tests\Unit\ApplicationServices\Admin;

use App\ApplicationServices\Admin\AuthApplicationServiceInterface;
use App\Models\Admin;
use App\Repositories\AdminRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class AuthApplicationServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var AdminRepositoryInterface $admin_repository_mock
     */
    private AdminRepositoryInterface $admin_repository_mock;

    /**
     * @var Admin $admin
     */
    private Admin $admin;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->admin_repository_mock = Mockery::mock(AdminRepositoryInterface::class);

        // TODO: DBに依存しているテスト
        $this->admin = Admin::factory()->create([
            'password' => Hash::make('password'),
        ]);
    }

    /**
     * TODO: DBに依存しているテスト
     *
     * @return void
     */
    public function testLogin(): void
    {
        // データ
        $expected_name = $this->admin->name;
        $expected_email = $this->admin->email;
        $expected_password = 'password';
        $expected_admin = $this->admin;

        // モックの設定
        $this->admin_repository_mock->shouldReceive('findById')
            ->once()
            ->with($expected_admin->id)
            ->andReturn($expected_admin);

        $this->app->instance(AdminRepositoryInterface::class, $this->admin_repository_mock);

        // インスタンス生成
        $auth_application_service = $this->app->make(AuthApplicationServiceInterface::class);

        // 実行
        $admin = $auth_application_service->login(
            [
                'email' => $expected_email,
                'password' => $expected_password,
            ]
        );

        // 検証
        $this->assertInstanceOf(Admin::class, $admin);
        $this->assertSame($expected_name, $admin->name);
        $this->assertSame($expected_email, $admin->email);
    }

    /**
     * TODO: DBに依存しているテスト
     *
     * @return void
     */
    public function testLoginAuthenticationException(): void
    {
        // データ
        $expected_email = $this->admin->email;
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

    /**
     * TODO: DBに依存しているテスト
     *
     * @return void
     */
    public function testGetLoginAdmin(): void
    {
        // データ
        $expected_name = $this->admin->name;
        $expected_email = $this->admin->email;
        $expected_password = 'password';
        $expected_admin = $this->admin;

        // モックの設定
        $this->admin_repository_mock->shouldReceive('findById')
            ->twice()
            ->with($expected_admin->id)
            ->andReturn($expected_admin);

        $this->app->instance(AdminRepositoryInterface::class, $this->admin_repository_mock);

        // インスタンス生成
        $auth_application_service = $this->app->make(AuthApplicationServiceInterface::class);

        // ログイン
        // TODO: ログインしないと正常に動作しない
        $auth_application_service->login(
            [
                'email' => $expected_email,
                'password' => $expected_password,
            ]
        );

        // 実行
        $admin = $auth_application_service->getLoginAdmin();

        // 検証
        $this->assertInstanceOf(Admin::class, $admin);
        $this->assertSame($expected_name, $admin->name);
        $this->assertSame($expected_email, $admin->email);
    }

    /**
     * TODO: DBに依存しているテスト
     *
     * @return void
     */
    public function testGetLogoutAdmin(): void
    {
        // インスタンス生成
        $auth_application_service = $this->app->make(AuthApplicationServiceInterface::class);

        // 実行
        $admin = $auth_application_service->getLoginAdmin();

        // 検証
        $this->assertNull($admin);
    }
}
