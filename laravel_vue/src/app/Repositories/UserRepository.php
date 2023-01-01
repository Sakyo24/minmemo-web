<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    /**
     * ユーザー作成
     *
     * @param array $params
     * @return User
     */
    public function create(array $params): User
    {
        $user = new User();

        $user->fill($params)->save();

        return $user;
    }

    /**
     * ユーザー取得
     *
     * @param int $user_id
     * @return User
     */
    public function findById(int $user_id): User
    {
        $user = User::find($user_id);

        return $user;
    }
}
