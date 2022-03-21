<?php

namespace App\Observers;

use App\Models\Point;
use App\Models\Withdraw;

class WithdrawObserver
{
    /**
     * Handle the Withdraw "created" event.
     *
     * @param  \App\Models\Withdraw  $withdraw
     * @return void
     */
    public function created(Withdraw $withdraw)
    {
        $withdraw->user->decreasePoint($withdraw->point, $withdraw->amount, 'withdraw');
    }

    /**
     * Handle the Withdraw "updated" event.
     *
     * @param  \App\Models\Withdraw  $withdraw
     * @return void
     */
    public function updated(Withdraw $withdraw)
    {
        //
    }

    /**
     * Handle the Withdraw "deleted" event.
     *
     * @param  \App\Models\Withdraw  $withdraw
     * @return void
     */
    public function deleted(Withdraw $withdraw)
    {
        //
    }

    /**
     * Handle the Withdraw "restored" event.
     *
     * @param  \App\Models\Withdraw  $withdraw
     * @return void
     */
    public function restored(Withdraw $withdraw)
    {
        //
    }

    /**
     * Handle the Withdraw "force deleted" event.
     *
     * @param  \App\Models\Withdraw  $withdraw
     * @return void
     */
    public function forceDeleted(Withdraw $withdraw)
    {
        //
    }
}
