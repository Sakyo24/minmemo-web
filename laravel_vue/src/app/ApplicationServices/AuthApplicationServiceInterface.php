<?php

declare(strict_types=1);

namespace App\ApplicationServices;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use RuntimeException;

interface AuthApplicationServiceInterface
{
    /**
     * ユーザー登録処理
     *
     * @param array $input
     * @return User
     * @throws RuntimeException
     */
    public function register(array $input): User;

    /**
     * ログイン処理
     *
     * @param array $input
     * @return User
     * @throws AuthenticationException
     */
    public function login(array $input): User;

    /**
     * ログアウト処理
     *
     * @return void
     */
    public function logout(): void;
}
