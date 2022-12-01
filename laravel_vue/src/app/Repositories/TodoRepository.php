<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Todo;
use Illuminate\Database\Eloquent\Collection;

class TodoRepository implements TodoRepositoryInterface
{
    /**
     * todo一覧全取得
     *
     * @return Collection
     */
    public function findAll(): Collection
    {
        return Todo::orderByDesc('id')->get();
    }
}
