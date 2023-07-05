<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Todo;
use App\Http\Controllers\Controller;
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
    public function index(): JsonResponse
    {
        $todos = Todo::with('user')->with('group')->paginate(20);

        return response()->json([
            'todos' => $todos
        ]);
    }
}
