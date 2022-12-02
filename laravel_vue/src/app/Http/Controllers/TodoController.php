<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;

class TodoController extends Controller
{
    public function index()
    {
        $todos = Todo::all();

        return response()->json([
            'todos' => $todos
        ]);
    }

    public function store(Request $request)
    {
        $todo = new Todo();
        $todo->fill($request->all())->save();
    }

    public function update(Request $request, Todo $todo)
    {
        $todo->fill($request->all())->update();

        return response()->json([
            'todo' => $todo
        ]);
    }

    public function destroy(Todo $todo)
    {
    }
}
