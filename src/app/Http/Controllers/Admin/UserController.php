<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;


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

    /**
     * user取得
     *
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * user更新
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $input = $request->only(['name', 'email']);
            $user->fill($input)->update();
        } catch (Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * user削除
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        try {
            $user->delete();
        } catch (Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
