<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\AuthController;

use App\Mail\Admin\VerificationMail;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class InviteActionTest extends TestCase
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
    public function testInvite(): void
    {
        // データ
        $expected_id = 2;
        $expected_name = Str::random(255);
        $expected_email = 'text@example.com';
        $expected_url = Str::random();
        $expected_password = Str::random();

        // リクエスト
        $response = $this->actingAs($this->admin, 'admin')->postJson('/api/admin/invite', [
            'name' => $expected_name,
            'email' => $expected_email,
        ]);

        // データ取得
        $admin = Admin::find($expected_id);

        // 検証
        $response->assertCreated()
            ->assertJsonStructure([
                    'admin' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
            ])
            ->assertExactJson([
                    'admin' => [
                        'id' => $expected_id,
                        'name' => $expected_name,
                        'email' => $expected_email,
                        'created_at' => (string)$admin->created_at,
                        'updated_at' => (string)$admin->updated_at,
                    ],
            ]);

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
    public function testInviteByUser(): void
    {
        // モック
        Mail::fake();

        // データ
        $expected_id = 2;
        $expected_name = Str::random(255);
        $expected_email = 'text@example.com';

        // リクエスト
        $response = $this->actingAs($this->user)->postJson('/api/admin/invite', [
            'name' => $expected_name,
            'email' => $expected_email,
        ]);

        // データ取得
        $admin = Admin::find($expected_id);

        // 検証
        $response->assertUnauthorized()
            ->assertJsonStructure([
                'message'
            ])
            ->assertExactJson([
                'message' => 'Unauthenticated.',
            ]);

        $this->assertNull($admin);

        Mail::assertNothingSent();
    }

    /**
     * @return void
     */
    public function testInviteByLogout(): void
    {
        // モック
        Mail::fake();

        // データ
        $expected_id = 2;
        $expected_name = Str::random(255);
        $expected_email = 'text@example.com';

        // リクエスト
        $response = $this->postJson('/api/admin/invite', [
            'name' => $expected_name,
            'email' => $expected_email,
        ]);

        // データ取得
        $admin = Admin::find($expected_id);

        // 検証
        $response->assertUnauthorized()
            ->assertJsonStructure([
                'message'
            ])
            ->assertExactJson([
                'message' => 'Unauthenticated.',
            ]);

        $this->assertNull($admin);

        Mail::assertNothingSent();
    }

    /**
     * @return void
     */
    public function testAllRequiredErrors(): void
    {
        // モック
        Mail::fake();

        // データ
        $expected_id = 2;

        // リクエスト
        $response = $this->actingAs($this->admin, 'admin')->postJson('/api/admin/invite', []);

        // データ取得
        $admin = Admin::find($expected_id);

        // 検証
        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'email',
            ]);

        $this->assertNull($admin);

        Mail::assertNothingSent();
    }

    /**
     * @return void
     */
    public function testNameMaxError(): void
    {
        // モック
        Mail::fake();

        // データ
        $expected_id = 2;
        $expected_name = Str::random(256);
        $expected_email = 'text@example.com';

        // リクエスト
        $response = $this->actingAs($this->admin, 'admin')->postJson('/api/admin/invite', [
            'name' => $expected_name,
            'email' => $expected_email,
        ]);

        // データ取得
        $admin = Admin::find($expected_id);

        // 検証
        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
            ]);

        $this->assertNull($admin);

        Mail::assertNothingSent();
    }

    /**
     * @return void
     */
    public function testEmailUniqueError(): void
    {
        // モック
        Mail::fake();

        // データ
        $expected_id = 2;
        $expected_name = Str::random(255);
        $expected_email = $this->admin->email;

        // リクエスト
        $response = $this->actingAs($this->admin, 'admin')->postJson('/api/admin/invite', [
            'name' => $expected_name,
            'email' => $expected_email,
        ]);

        // データ取得
        $admin = Admin::find($expected_id);

        // 検証
        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email',
            ]);

        $this->assertNull($admin);

        Mail::assertNothingSent();
    }

    /**
     * @return void
     */
    public function testEmailMaxError(): void
    {
        // モック
        Mail::fake();

        // データ
        $expected_id = 2;
        $expected_name = Str::random(255);
        $expected_email = Str::random(245) . 'example.com';

        // リクエスト
        $response = $this->actingAs($this->admin, 'admin')->postJson('/api/admin/invite', [
            'name' => $expected_name,
            'email' => $expected_email,
        ]);

        // データ取得
        $admin = Admin::find($expected_id);

        // 検証
        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email',
            ]);

        $this->assertNull($admin);

        Mail::assertNothingSent();
    }

    /**
     * @return void
     */
    public function testEmailEmailFilterError(): void
    {
        // モック
        Mail::fake();

        // データ
        $expected_id = 2;
        $expected_name = Str::random(255);
        $expected_email = Str::random(255);

        // リクエスト
        $response = $this->actingAs($this->admin, 'admin')->postJson('/api/admin/invite', [
            'name' => $expected_name,
            'email' => $expected_email,
        ]);

        // データ取得
        $admin = Admin::find($expected_id);

        // 検証
        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email',
            ]);

        $this->assertNull($admin);

        Mail::assertNothingSent();
    }
}
