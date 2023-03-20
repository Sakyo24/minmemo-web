<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Admin;

interface AdminRepositoryInterface
{
    /**
     * 管理者取得
     *
     * @param int $admin_id
     * @return Admin
     */
    public function findById(int $admin_id): Admin;

    /**
     * 管理者作成
     *
     * @param array $params
     * @return Admin
     */
    public function create(array $params): Admin;
}
