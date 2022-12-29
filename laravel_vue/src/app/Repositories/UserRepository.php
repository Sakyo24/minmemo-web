<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    /**
     * ユーザー作成
     *
     * @param array
     * @return User
     */
    public function create(array $params): User
    {
        $user = new User();

        $user->fill($params)->save();

        return $user;
    }
}
