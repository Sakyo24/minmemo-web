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
}
