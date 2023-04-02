<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

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
     * @param StoreTodoRequest $request
     * @return JsonResponse
     */
    public function store(StoreTodoRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $input = $request->all();
            $input['user_id'] = $user->id;
            $todo = new Todo();
            $todo->fill($input)->save();
        } catch (Throwable $e) {
            Log::error((string)$e);

            throw $e;
        }

        return response()->json([], Response::HTTP_CREATED);
    }

    /**
     * todo更新
     *
     * @param UpdateTodoRequest $request
     * @param Todo $todo
     * @return JsonResponse
     */
    public function update(UpdateTodoRequest $request, Todo $todo): JsonResponse
    {
        try {
            $user = $request->user();
            $input = $request->all();
            $input['user_id'] = $user->id;
            $todo->fill($input)->update();
        } catch (Throwable $e) {
            Log::error((string)$e);

            throw $e;
        }

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
        try {
            $todo->delete();
        } catch (Throwable $e) {
            Log::error((string)$e);

            throw $e;
        }

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
