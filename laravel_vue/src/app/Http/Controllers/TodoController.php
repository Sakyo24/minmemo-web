<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Todo;
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
        try {
            $todo = new Todo();
            $todo->fill($request->all())->save();
        } catch (Throwable $e) {
            Log::error((string)$e);

            throw $e;
        }

        return response()->json(
            [],
            Response::HTTP_CREATED
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
        try {
            $todo->fill($request->all())->update();
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

        return response()->json(
            [],
            Response::HTTP_NO_CONTENT
        );
    }
}
