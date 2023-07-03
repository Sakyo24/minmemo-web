<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
// use Illuminate\Http\Request;


class UserController extends Controller
{
    /**
     * user一覧全取得
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = User::paginate(20);

        return response()->json([
            'users' => $users
        ]);
    }
}
