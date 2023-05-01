<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Admin;

class AdminRepository implements AdminRepositoryInterface
{
    /**
     * 管理者取得
     *
     * @param int $admin_id
     * @return Admin | null
     */
    public function findById(int $admin_id): ?Admin
    {
        $admin = Admin::find($admin_id);

        return $admin;
    }

    /**
     * 管理者作成
     *
     * @param array $params
     * @return Admin
     */
    public function create(array $params): Admin
    {
        $admin = new Admin();

        $admin->fill($params)->save();

        return $admin;
    }
}
