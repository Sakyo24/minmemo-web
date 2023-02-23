<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;

class MobileAuthController extends Controller
{
    /**
     * ユーザー登録
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $input = $request->only(['name', 'email', 'password']);

        $input['password'] = Hash::make($input['password']);
        User::create($input);

        return response()->json([], Response::HTTP_CREATED);
    }

    /**
     * ログイン
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $input = $request->only(['email', 'password']);

        $user = User::where('email', $input['email'])->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => trans('auth.failed')
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'token' => $user->createToken($user->name)->plainTextToken,
            'user' => $user
        ]);
    }

    /**
     * ログアウト
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = User::where('email', $request->input('email'))->first();
            $user->tokens()->delete();
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message' => 'ログアウト処理に失敗しました。'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
