<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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
}
