<?php

declare(strict_types=1);

namespace App\ApplicationServices;

use App\Repositories\TodoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

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
     * @throws RuntimeException
     */
    public function create(array $input): void
    {
        DB::beginTransaction();

        try {
            $this->todo_repository->create($input);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error((string)$e);

            throw new RuntimeException();
        }
    }
}
