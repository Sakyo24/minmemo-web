<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Admin;
use App\Policies\AdminPolicy;
// use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
// use Illuminate\Support\Carbon;
// use Illuminate\Support\Facades\Config;
// use Illuminate\Support\Facades\Gate;
// use Illuminate\Support\Facades\URL;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Admin::class => AdminPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // 認証URLのカスタマイズ
        // VerifyEmail::createUrlUsing(function ($notifiable) {
        //     return URL::temporarySignedRoute(
        //         'verification.verify',
        //         Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
        //         [
        //             'id' => $notifiable->getKey(),
        //             'hash' => sha1($notifiable->getEmailForVerification()),
        //         ]
        //     );
        // });

        // 認証メールのカスタマイズ
        // VerifyEmail::toMailUsing(function ($notifiable, $url) {
        //     return new VerificationMail($notifiable->getEmailForVerification(), $url);
        // });
    }
}
