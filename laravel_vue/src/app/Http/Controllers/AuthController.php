<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\ApplicationServices\AuthApplicationServiceInterface;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;
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
            Log::error($e->getMessage());
            throw new RuntimeException($e->getMessage());
        }

        return response()->json([
            'user' => $user,
        ], 201);
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
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode());
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

        return response()->json([], 204);
    }
}
