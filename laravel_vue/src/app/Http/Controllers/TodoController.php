<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * todo一覧全取得
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $todos = $user->todos()->whereNull('group_id')->get();

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
        $user = $request->user();
        $input = $request->all();
        $input['user_id'] = $user->id;
        $todo = new Todo();
        $todo->fill($input)->save();

        return response()->json([], Response::HTTP_CREATED);
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
        $user = $request->user();
        $input = $request->all();
        $input['user_id'] = $user->id;
        $todo->fill($input)->update();

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

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
