<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\ApplicationServices\Admin\AuthApplicationServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InviteRequest;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use RuntimeException;

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

    /**
     * 管理者の招待
     *
     * @param InviteRequest $request
     * @return JsonResponse
     */
    public function invite(InviteRequest $request): JsonResponse
    {
        $input = $request->only(['name', 'email']);

        $admin = $this->auth_service->invite($input);

        return response()->json([
            'admin' => $admin
        ], Response::HTTP_CREATED);
    }

    /**
     * メールアドレス認証
     *
     * @param int $id
     * @param string $hash
     * @return RedirectResponse
     */
    public function verify(int $id, string $hash): RedirectResponse
    {
        $input = [
            'id' => $id,
            'hash' => $hash,
        ];

        try {
            $this->auth_service->verify($input);
        } catch(RuntimeException $e) { // TODO: 独自例外を実装してcatchしたい
            Log::error((string)$e);

            // TODO: エラー内容がわかるような独自ビューを返したい
            abort(Response::HTTP_BAD_REQUEST);
        }

        return redirect('/admin/login');
    }
}
