<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Todo;

class TodoController extends Controller
{
    /**
     * todo一覧全取得
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $todos = Todo::all();

        return response()->json([
            'todos' => $todos
        ]);
    }

    /**
     * todo新規作成
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $todo = new Todo();
        $todo->fill($request->all())->save();

        return response()->json(
            [], 201
        );
    }

    /**
     * todo更新
     *
     * @param Request $request
     * @param Todo $todo
     * @return JsonResponse
     */
    public function update(Request $request, Todo $todo): JsonResponse
    {
        $todo->fill($request->all())->update();

        return response()->json([
            'todo' => $todo
        ]);
    }

    /**
     * todo削除
     *
     * @param Todo $todo
     * @return JsonResponse
     */
    public function destroy(Todo $todo): JsonResponse
    {
        $todo->delete();

        return response()->json(
            [], 204
        );
    }
}
