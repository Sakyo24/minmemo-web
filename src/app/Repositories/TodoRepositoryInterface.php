<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Todo;
use Illuminate\Database\Eloquent\Collection;

interface TodoRepositoryInterface
{
    /**
     * todo一覧全取得
     *
     * @return Collection
     */
    public function findAll(): Collection;

    /**
     * todo登録
     *
     * @param array
     * @return Todo
     */
    public function create(array $params): Todo;
}
