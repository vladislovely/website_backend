<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        ResetPassword::createUrlUsing(static function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/admin/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        VerifyEmail::createUrlUsing(static function (object $notifiable) {
            $params = [
                'id'      => $notifiable->getKey(),
                'hash'    => sha1($notifiable->getEmailForVerification()),
                'expires' => Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60))->timestamp,
            ];

            $url       = URL::route('verification.verify', $params);
            $key       = config('app.key');
            $signature = hash_hmac('sha256', $url, $key);

            return config('app.frontend_url') . "/admin/verify-email/{$params['id']}/{$params['hash']}?expires={$params['expires']}&signature={$signature}";
        });

        Gate::define('create-user', [UserPolicy::class, 'create']);
        Gate::define('update-user', [UserPolicy::class, 'update']);
        Gate::define('view-user', [UserPolicy::class, 'view']);
        Gate::define('delete-user', [UserPolicy::class, 'delete']);
    }
}
