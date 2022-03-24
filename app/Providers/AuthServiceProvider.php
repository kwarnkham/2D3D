<?php

namespace App\Providers;

use App\Models\TopUp;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('cancel-top-up', function (User $user, TopUp $topUp) {
            return $user->id == $topUp->user->id;
        });

        Gate::define('cancel-withdraw', function (User $user, Withdraw $withdraw) {
            return $user->id == $withdraw->user->id;
        });
    }
}
