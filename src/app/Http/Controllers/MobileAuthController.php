<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\PasswordResetSendMailRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Throwable;

class MobileAuthController extends Controller
{
    use SendsPasswordResetEmails;

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

        try {
            User::create($input);
        } catch (Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

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
        } catch (Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * ログイン中ユーザー
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function loginUser(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * パスワードリセットメールの送信
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendResetLinkEmail(PasswordResetSendMailRequest $request): JsonResponse
    {
        $response = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? response()->json([
                'status' => 'success'
            ])
            : response()->json([
                'status' => 'error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * パスワードリセット
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reset(PasswordResetRequest $request)
    {
        $response = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $response == Password::PASSWORD_RESET
            ? response()->json([
                'status' => 'success'
            ])
            : response()->json([
                'status' => 'error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
