<?php

declare(strict_types=1);

namespace Tests\Unit\ApplicationServices\Admin;

use App\ApplicationServices\Admin\AuthApplicationServiceInterface;
use App\Mail\Admin\VerificationMail;
use App\Mail\Admin\VerifiedMail;
use App\Models\Admin;
use App\Repositories\AdminRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Mockery;
use RuntimeException;
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

    /**
     * @return void
     */
    public function testInvite(): void
    {
        // データ
        $expected_id = 1;
        $expected_name = Str::random();
        $expected_email = Str::random() . '@example.com';
        $expected_password = Str::random();
        $expected_url = Str::random();

        $expected_admin = Admin::factory()->make([
            'id' => $expected_id,
            'name' => $expected_name,
            'email' => $expected_email,
            'password' => $expected_password,
        ]);

        // モックの設定
        $this->admin_repository_mock->shouldReceive('create')
            ->once()
            ->andReturn($expected_admin);

        $this->app->instance(AdminRepositoryInterface::class, $this->admin_repository_mock);

        // インスタンス生成
        $auth_application_service = $this->app->make(AuthApplicationServiceInterface::class);

        // 実行
        $admin = $auth_application_service->invite([
            'name' => $expected_name,
            'email' => $expected_email,
        ]);

        // 検証
        $this->assertSame($expected_id, $admin->id);
        $this->assertSame($expected_name, $admin->name);
        $this->assertSame($expected_email, $admin->email);

        // TODO: 本当はモック化したいが、モック化すると内容の検証ができない。
        $mailable = new VerificationMail($expected_url, $expected_password);
        // TODO: 上手く動作しない
        // $mailable->assertTo($expected_email);
        $mailable->assertHasSubject('管理画面への招待');
        $mailable->assertSeeInHtml($expected_url);
        $mailable->assertSeeInHtml($expected_password);
        $mailable->assertSeeInHtml('管理者として招待されました。');
    }

    /**
     * @return void
     */
    public function testVerify(): void
    {
        // データ
        $expected_id = 2;
        $expected_email = Str::random() . '@example.com';
        $expected_name = Str::random();
        $expected_hash = sha1($expected_email);
        $expected_password = Str::random();

        $expected_admin = Admin::factory()->make([
            'id' => $expected_id,
            'name' => $expected_name,
            'email' => $expected_email,
            'password' => $expected_password,
            'email_verified_at' => null,
        ]);

        // モックの設定
        $this->admin_repository_mock->shouldReceive('findById')
            ->once()
            ->with($expected_id)
            ->andReturn($expected_admin);

        $this->app->instance(AdminRepositoryInterface::class, $this->admin_repository_mock);

        // インスタンス生成
        $auth_application_service = $this->app->make(AuthApplicationServiceInterface::class);

        // 実行
        $auth_application_service->verify([
            'id' => $expected_id,
            'hash' => $expected_hash,
        ]);

        // 検証
        // TODO: 本当はモック化したいが、モック化すると内容の検証ができない。
        $mailable = new VerifiedMail();
        // TODO: 上手く動作しない
        // $mailable->assertTo($expected_email);
        $mailable->assertHasSubject('メールアドレスの認証完了');
        $mailable->assertSeeInHtml('管理者としてのメールアドレスの認証が完了しました。');
    }

    /**
     * @return void
     */
    public function testVerifyIdError(): void
    {
        // モック
        Mail::fake();

        // データ
        $expected_id = 1;
        $expected_email = Str::random() . '@example.com';
        $expected_hash = sha1($expected_email);

        // モックの設定
        $this->admin_repository_mock->shouldReceive('findById')
            ->once()
            ->with($expected_id)
            ->andReturn(null);

        $this->app->instance(AdminRepositoryInterface::class, $this->admin_repository_mock);

        // インスタンス生成
        $auth_application_service = $this->app->make(AuthApplicationServiceInterface::class);

        // 検証
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('存在しないIDです。');

        // 実行
        $auth_application_service->verify([
            'id' => $expected_id,
            'hash' => $expected_hash,
        ]);

        // 検証
        Mail::assertNothingSent();
    }

    /**
     * @return void
     */
    public function testVerifyHashError(): void
    {
        // モック
        Mail::fake();

        // データ
        $expected_id = 1;
        $expected_name = Str::random();
        $expected_email = Str::random() . '@example.com';
        $expected_password = Str::random();
        $expected_hash = sha1(Str::random());

        $expected_admin = Admin::factory()->make([
            'id' => $expected_id,
            'name' => $expected_name,
            'email' => $expected_email,
            'password' => $expected_password,
            'email_verified_at' => null,
        ]);

        // モックの設定
        $this->admin_repository_mock->shouldReceive('findById')
            ->once()
            ->with($expected_id)
            ->andReturn($expected_admin);

        $this->app->instance(AdminRepositoryInterface::class, $this->admin_repository_mock);

        // インスタンス生成
        $auth_application_service = $this->app->make(AuthApplicationServiceInterface::class);

        // 検証
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('不正な値です。');

        // 実行
        $auth_application_service->verify([
            'id' => $expected_id,
            'hash' => $expected_hash,
        ]);

        // 検証
        Mail::assertNothingSent();
    }

    /**
     * @return void
     */
    public function testVerifyEmailVerifiedAtError(): void
    {
        // モック
        Mail::fake();

        // データ
        $expected_id = 1;
        $expected_name = Str::random();
        $expected_email = Str::random() . '@example.com';
        $expected_password = Str::random();
        $expected_hash = sha1($expected_email);

        $expected_admin = Admin::factory()->make([
            'id' => $expected_id,
            'name' => $expected_name,
            'email' => $expected_email,
            'password' => $expected_password,
            'email_verified_at' => Carbon::now(),
        ]);

        // モックの設定
        $this->admin_repository_mock->shouldReceive('findById')
            ->once()
            ->with($expected_id)
            ->andReturn($expected_admin);

        $this->app->instance(AdminRepositoryInterface::class, $this->admin_repository_mock);

        // インスタンス生成
        $auth_application_service = $this->app->make(AuthApplicationServiceInterface::class);

        // 検証
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('既に認証済みです。');

        // 実行
        $auth_application_service->verify([
            'id' => $expected_id,
            'hash' => $expected_hash,
        ]);

        // 検証
        Mail::assertNothingSent();
    }
}
