<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserController extends Controller
{
    /**
     * ユーザー更新
     *
     * @param UpdateUserRequest $request
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = $request->user();
        $input = $request->only(['name', 'email']);

        try {
            DB::transaction(function () use ($input, $user) {
                $user->fill($input)->update();
            });
        } catch (Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * パスワード更新
     *
     * @param UpdatePasswordRequest $request
     * @return JsonResponse
     */
    public function passwordUpdate(UpdatePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $input = $request->only(['password']);
        $input['password'] = Hash::make($input['password']);

        try {
            DB::transaction(function () use ($input, $user) {
                $user->fill($input)->update();
            });
        } catch (Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

        return response()->json([
            'user' => $user
        ]);
    }
}
