<?php

namespace App\Observers;

use App\Models\Point;
use App\Models\Withdraw;
use App\Services\TelegramService;

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
        TelegramService::sendAdminMessage("Received a new withdraw from user id of " . $withdraw->user->id . " <a href='" . $withdraw->getApproveLink() . "'>here</a>");
        $withdraw->user->decreasePoint($withdraw->point, $withdraw->amount, 'submit withdraw', $withdraw);
    }

    /**
     * Handle the Withdraw "updated" event.
     *
     * @param  \App\Models\Withdraw  $withdraw
     * @return void
     */
    public function updated(Withdraw $withdraw)
    {
        if ($withdraw->status == 2) {
            $withdraw->user->decreaseReferrablePoint($withdraw->point, $withdraw->amount);
            $withdraw->user->notify(__("messages.Withdraw has been approved"));
        }
        if ($withdraw->status == 3) $withdraw->user->increasePoint($withdraw->point, $withdraw->amount, 'withdraw rejected', $withdraw);
        if ($withdraw->status == 5) $withdraw->user->increasePoint($withdraw->point, $withdraw->amount, 'withdraw canceled', $withdraw);
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
