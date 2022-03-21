<?php

namespace App\Providers;

use App\Models\TopUp;
use App\Models\TwoDigitHit;
use App\Models\User;
use App\Models\Withdraw;
use App\Observers\TopUpObserver;
use App\Observers\TwoDigitHitObserver;
use App\Observers\UserObserver;
use App\Observers\WithdrawObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        // TwoDigit::observe(TwoDigitObserver::class);
        TwoDigitHit::observe(TwoDigitHitObserver::class);
        TopUp::observe(TopUpObserver::class);
        Withdraw::observe(WithdrawObserver::class);
    }
}
