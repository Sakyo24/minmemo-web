<?php

declare(strict_types=1);

namespace App\ApplicationServices;

use Illuminate\Database\Eloquent\Collection;

interface TodoApplicationServiceInterface
{
    /**
     * todo一覧全取得
     *
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * todo新規作成
     *
     * @param array $input
     * @return void
     */
    public function create(array $input): void;
}
