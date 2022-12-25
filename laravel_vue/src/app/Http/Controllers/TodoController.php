<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;

class TodoController extends Controller
{
    /**
     * todo一覧全取得
     * @return Collection
     */
    public function index()
    {
        $todos = Todo::all();

        return response()->json([
            'todos' => $todos
        ]);
    }

    /**
     * todo新規作成
     */
    public function store(Request $request)
    {
        $todo = new Todo();
        $todo->fill($request->all())->save();

        return response()->json(
            [],201
        );
    }

    public function edit(Todo $todo)
    {
    }

    /**
     * todo更新
     */
    public function update(Request $request, Todo $todo)
    {
    }

    /**
     * todo削除
     */
    public function destroy(Todo $todo)
    {
        $todo->delete();

        return response()->json(
            [],204
        );
    }
}
