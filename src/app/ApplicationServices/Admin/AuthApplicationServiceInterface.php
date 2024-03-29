<?php

declare(strict_types=1);

namespace App\ApplicationServices\Admin;

use App\Models\Admin;
use Illuminate\Auth\AuthenticationException;

interface AuthApplicationServiceInterface
{
    /**
     * ログイン処理
     *
     * @param array $input
     * @return Admin
     * @throws AuthenticationException
     */
    public function login(array $input): Admin;

    /**
     * ログアウト処理
     *
     * @return void
     */
    public function logout(): void;

    /**
     * ログイン中の管理者取得処理
     *
     * @return Admin|null
     */
    public function getLoginAdmin(): ?Admin;

    /**
     * 管理者の招待
     *
     * @param array $input
     * @return Admin
     */
    public function invite(array $input): Admin;

    /**
     * 管理者のメールアドレス認証
     *
     * @param array $input
     * @return void
     */
    public function verify(array $input): void;
}
