<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PasswordResetRequest;
use App\Http\Requests\Admin\PasswordUpdateRequest;
// use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    public function reset(PasswordResetRequest $request)
    {
        $response = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($admin, $password) {
                $admin->password = Hash::make($password);
                $admin->save();
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

    public function update(PasswordUpdateRequest $request)
    {
        $admin = Auth::user();
        if(!password_verify($request->current_password, $admin->password))
        {
            return response()->json([
                'message' => '現在のパスワードが間違っています。',
                'status' => 'error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $admin->password = Hash::make($request->input('new_password'));
        $admin->save();

        return response()->json([
            'status' => 'success'
        ]);
    }
}
