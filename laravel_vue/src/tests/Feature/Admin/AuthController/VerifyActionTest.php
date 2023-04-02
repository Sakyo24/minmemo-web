<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\AuthController;

use App\Mail\Admin\VerifiedMail;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class VerifyActionTest extends TestCase
{
    use RefreshDatabase;

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

        $this->admin = Admin::factory()->create([
            'email_verified_at' => null,
        ]);
    }

    /**
     * @return void
     */
    public function testVerify(): void
    {
        // 一時URLの生成
        $url = URL::temporarySignedRoute(
            'admin.verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $this->admin->id,
                'hash' => sha1($this->admin->email),
            ]
        );

        // リクエスト
        $response = $this->get($url);

        // データ取得
        $admin = Admin::find($this->admin->id);

        // 検証
        $response->assertRedirect('/admin/login');

        $this->assertNotNull($admin->email_verified_at);

        // TODO: 本当はモック化したいが、モック化すると内容の検証ができない。
        $mailable = new VerifiedMail();
        // TODO: 上手く動作しない
        // $mailable->assertTo($admin->email);
        $mailable->assertHasSubject('メールアドレスの認証完了');
        $mailable->assertSeeInHtml('管理者としてのメールアドレスの認証が完了しました。');

        $this->assertGuest('admin');
    }

    /**
     * @return void
     */
    public function testIdError(): void
    {
        // モック
        Mail::fake();

        // 一時URLの生成
        $url = URL::temporarySignedRoute(
            'admin.verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => 2,
                'hash' => sha1($this->admin->email),
            ]
        );

        // リクエスト
        $response = $this->get($url);

        // 検証
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        Mail::assertNothingSent();
    }

    /**
     * @return void
     */
    public function testHashError(): void
    {
        // モック
        Mail::fake();

        // 一時URLの生成
        $url = URL::temporarySignedRoute(
            'admin.verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $this->admin->id,
                'hash' => sha1('test@example.com'),
            ]
        );

        // リクエスト
        $response = $this->get($url);

        // 検証
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        Mail::assertNothingSent();
    }

    /**
     * @return void
     */
    public function testEmailVerifiedAtError(): void
    {
        // モック
        Mail::fake();

        // データ
        $admin = Admin::factory()->create([
            'email_verified_at' => Carbon::now(),
        ]);

        // 一時URLの生成
        $url = URL::temporarySignedRoute(
            'admin.verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $admin->id,
                'hash' => sha1($admin->email),
            ]
        );

        // リクエスト
        $response = $this->get($url);

        // 検証
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        Mail::assertNothingSent();
    }
}
