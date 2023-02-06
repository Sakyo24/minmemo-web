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
}
