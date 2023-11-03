<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\PasswordResetSendMailRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\VerificationMail;
use App\Mail\VerifiedMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
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
            $user = DB::transaction(function () use ($input) {
                $user = User::create($input);
                return $user;
            });
            // 認証URLの生成
            $url = URL::temporarySignedRoute(
                'user.verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $user->id,
                    'hash' => sha1($user->email),
                ]
            );
            // メール送信
            Mail::to($user->email)->send(new VerificationMail($url));
        } catch (Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

        return response()->json([], Response::HTTP_CREATED);
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
            $user = User::find($input['id']);
            // IDチェック
            if (is_null($user)) {
                throw new Throwable('存在しないIDです。');
            }
            // ハッシュ値チェック
            if (!hash_equals((string)$input['hash'], sha1($user->email))) {
                throw new Throwable('不正な値です。');
            }
            // 既に認証済みかチェック
            if (!is_null($user->email_verified_at)) {
                throw new Throwable('既に認証済みです。');
            }
            // 認証処理
            DB::transaction(function () use ($user) {
                $user->forceFill([
                    'email_verified_at' => Carbon::now(),
                ])->save();
            });
            // 登録完了メールの送信
            Mail::to($user->email)->send(new VerifiedMail());
        } catch(Throwable $e) {
            Log::error((string)$e);
            throw $e;
        }

        return redirect('/user/verified');
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
