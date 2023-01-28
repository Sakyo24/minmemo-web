<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Auth;

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
        $user = User::create($input);
        $token = $user->createToken('appToken')->accessToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ], 200);
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

        if (Auth::attempt(['email' => $input['email'], 'password' => $input['password']])) {
            $user = Auth::user();
            // トークンを作成
            $token = $user->createToken('appToken')->accessToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
            ], 200);
        } else {
            return response()->json([
                'message' => '入力されたメールアドレスとパスワードに誤りがあります。'
            ], 401);
        }
    }

    /**
     * ログアウト
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $res)
    {
        if (Auth::check()) {
            // トークンの無効化
            $token = Auth::user()->token();
            $token->revoke();

            return response()->json([], 200);
        } else {
            return response()->json([
                'message' => '会員情報を取得できませんでした。'
            ], 401);
        }
    }
}
