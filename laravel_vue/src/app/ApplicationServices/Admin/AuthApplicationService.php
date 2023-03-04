<?php

declare(strict_types=1);

namespace App\ApplicationServices\Admin;

use App\Models\Admin;
use App\Repositories\AdminRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

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
}
