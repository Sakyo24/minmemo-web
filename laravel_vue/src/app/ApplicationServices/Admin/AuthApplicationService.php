<?php

declare(strict_types=1);

namespace App\ApplicationServices\Admin;

use App\Mail\Admin\VerificationMail;
use App\Mail\Admin\VerifiedMail;
use App\Models\Admin;
use App\Repositories\AdminRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use RuntimeException;

class AuthApplicationService implements AuthApplicationServiceInterface
{
    /**
     * @var AdminRepositoryInterface $admin_repository
     */
    private AdminRepositoryInterface $admin_repository;

    /**
     * コンストラクタ
     *
     * @param AdminRepositoryInterface $admin_repository
     */
    public function __construct(AdminRepositoryInterface $admin_repository)
    {
        $this->admin_repository = $admin_repository;
    }

    /**
     * ログイン処理
     *
     * @param array $input
     * @return Admin
     * @throws AuthenticationException
     */
    public function login(array $input): Admin
    {
        if (!Auth::guard('admin')->attempt($input)) {
            throw new AuthenticationException();
        }

        $admin_id = Auth::guard('admin')->id();
        $admin = $this->admin_repository->findById($admin_id);

        return $admin;
    }

    /**
     * ログアウト処理
     *
     * @return void
     */
    public function logout(): void
    {
        Auth::guard('admin')->logout();
    }

    /**
     * ログイン中の管理者取得処理
     *
     * @return Admin|null
     */
    public function getLoginAdmin(): ?Admin
    {
        $admin_id = Auth::guard('admin')->id();

        if (isset($admin_id)) {
            $admin = $this->admin_repository->findById($admin_id);

            return $admin;
        }

        return null;
    }

    /**
     * 管理者の招待
     *
     * @param array $input
     * @return Admin
     */
    public function invite(array $input): Admin
    {
        // 初期パスワード生成
        $password = substr(bin2hex(random_bytes(16)), 0, 16);

        $input['password'] = Hash::make($password);

        $admin = DB::transaction(function () use ($input) {
            $admin = $this->admin_repository->create($input);

            return $admin;
        });

        // 認証URLの生成
        $url = URL::temporarySignedRoute(
            'admin.verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $admin->id,
                'hash' => sha1($admin->email),
            ]
        );

        // メール送信
        Mail::to($admin->email)->send(new VerificationMail($url, $password));

        return $admin;
    }

    /**
     * 管理者のメールアドレス認証
     *
     * @param array $input
     * @return void
     */
    public function verify(array $input): void
    {
        // 管理者の取得
        $admin = $this->admin_repository->findById($input['id']);

        // IDチェック
        if (is_null($admin)) {
            // TODO: 独自例外にしたい
            throw new RuntimeException('存在しないIDです。');
        }

        // ハッシュ値チェック
        if (!hash_equals((string)$input['hash'], sha1($admin->email))) {
            // TODO: 独自例外にしたい
            throw new RuntimeException('不正な値です。');
        }

        // 既に認証済みかチェック
        if (!is_null($admin->email_verified_at)) {
            // TODO: 独自例外にしたい
            throw new RuntimeException('既に認証済みです。');
        }

        // 認証処理
        DB::transaction(function () use ($admin) {
            $admin->forceFill([
                'email_verified_at' => Carbon::now(),
            ])->save();
        });

        // 登録完了メールの送信
        Mail::to($admin->email)->send(new VerifiedMail());
    }
}
