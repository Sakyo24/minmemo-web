<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface TodoRepositoryInterface
{
    /**
     * todo一覧全取得
     *
     * @return Collection
     */
    public function findAll(): Collection;
}
