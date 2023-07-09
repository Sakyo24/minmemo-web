<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Todo;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTodoRequest;
use Illuminate\Http\JsonResponse;
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
    public function index(): JsonResponse
    {
        $todos = Todo::with('user')->with('group')->paginate(20);

        return response()->json([
            'todos' => $todos
        ]);
    }

    /**
     * todo取得
     *
     * @param Todo $todo
     * @return JsonResponse
     */
    public function show(Todo $todo): JsonResponse
    {
        $todo = Todo::with('user')->with('group')->find($todo->id);

        return response()->json([
            'todo' => $todo
        ]);
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
            $input = $request->only(['title', 'detail']);
            $todo->fill($input)->update();
        } catch (Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

        return response()->json([
            'todo' => $todo
        ]);
    }
}
