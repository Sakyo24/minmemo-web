<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\ApplicationServices\AuthApplicationServiceInterface;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class AuthController extends Controller
{
    /**
     * @var AuthApplicationServiceInterface $auth_service
     */
    private AuthApplicationServiceInterface $auth_service;

    /**
     * コンストラクタ
     *
     * @param AuthApplicationInterface $auth_service
     */
    public function __construct(AuthApplicationServiceInterface $auth_service)
    {
        $this->auth_service = $auth_service;
    }

    /**
     * ユーザー登録
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $input = $request->only([
            'name',
            'email',
            'password',
        ]);

        try {
            $user = $this->auth_service->register($input);
        } catch (Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

        return response()->json([
            'user' => $user,
        ], Response::HTTP_CREATED);
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

        try {
            $user = $this->auth_service->login($input);

            $request->session()->regenerate();

            return response()->json([
                'user' => $user,
            ]);
        } catch (AuthenticationException $e) {
            Log::error((string)$e);

            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * ログアウト
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $this->auth_service->logout();

        $request->session()->regenerate();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * ログイン中のユーザー取得
     *
     * @return JsonResponse
     */
    public function getLoginUser(): JsonResponse
    {
        $user = $this->auth_service->getLoginUser();

        if (isset($user)) {
            return response()->json([
                'user' => $user
            ]);
        }

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
