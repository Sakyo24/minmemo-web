<?php

declare(strict_types=1);

namespace App\ApplicationServices;

use App\Repositories\TodoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TodoApplicationService implements TodoApplicationServiceInterface
{
    /**
     * @var TodoRepositoryInterface $todo_repository
     */
    private TodoRepositoryInterface $todo_repository;

    /**
     * コンストラクタ
     *
     * @param TodoRepositoryInterface $todo_repository
     */
    public function __construct(TodoRepositoryInterface $todo_repository)
    {
        $this->todo_repository = $todo_repository;
    }

    /**
     * todo一覧全取得
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        $todos = $this->todo_repository->findAll();

        return $todos;
    }

    /**
     * todo新規作成
     *
     * @param array $input
     * @return void
     */
    public function create(array $input): void
    {
        DB::transaction(function () use ($input) {
            $this->todo_repository->create($input);
        });
    }
}
