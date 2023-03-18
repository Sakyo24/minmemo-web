<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\ApplicationServices\Admin\AuthApplicationServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

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
     * ログイン
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $input = $request->only(['email', 'password']);

        try {
            $admin = $this->auth_service->login($input);

            $request->session()->regenerate();

            return response()->json([
                'admin' => $admin,
            ]);
        } catch (AuthenticationException $e) {
            Log::error((string)$e);

            return response()->json([
                'message' => __('auth.failed'),
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
     * ログイン中の管理者取得
     *
     * @return JsonResponse
     */
    public function getLoginAdmin(): JsonResponse
    {
        $admin = $this->auth_service->getLoginAdmin();

        if (isset($admin)) {
            return response()->json([
                'admin' => $admin
            ]);
        }

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
