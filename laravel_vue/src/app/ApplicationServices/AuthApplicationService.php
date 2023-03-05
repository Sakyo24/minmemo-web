<?php

declare(strict_types=1);

namespace App\ApplicationServices;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Throwable;

class AuthApplicationService implements AuthApplicationServiceInterface
{
    /**
     * @var UserRepositoryInterface $user_repository
     */
    private UserRepositoryInterface $user_repository;

    /**
     * コンストラクタ
     *
     * @param UserRepositoryInterface $user_repository
     */
    public function __construct(UserRepositoryInterface $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    /**
     * ユーザー登録処理
     *
     * @param array $input
     * @return User
     * @throws RuntimeException
     */
    public function register(array $input): User
    {
        $input['password'] = Hash::make($input['password']);

        try {
            $user = DB::transaction(function () use ($input) {
                $user = $this->user_repository->create($input);

                return $user;
            });

            return $user;
        } catch (Throwable $e) {
            throw new RuntimeException();
        }
    }

    /**
     * ログイン処理
     *
     * @param array $input
     * @return User
     * @throws AuthenticationException
     */
    public function login(array $input): User
    {
        if (!Auth::attempt($input)) {
            throw new AuthenticationException();
        }

        $user_id = Auth::id();
        $user = $this->user_repository->findById($user_id);

        return $user;
    }

    /**
     * ログアウト処理
     *
     * @return void
     */
    public function logout(): void
    {
        Auth::logout();
    }

    /**
     * ログイン中のユーザー取得処理
     *
     * @return User|null
     */
    public function getLoginUser(): ?User
    {
        $user_id = Auth::id();

        if (isset($user_id)) {
            $user = $this->user_repository->findById($user_id);

            return $user;
        }

        return null;
    }
}
