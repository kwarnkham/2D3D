<?php

namespace App\Observers;

use App\Models\Point;
use App\Models\TopUp;
use App\Services\TelegramService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class TopUpObserver
{
    /**
     * Handle the TopUp "created" event.
     *
     * @param  \App\Models\TopUp  $topUp
     * @return void
     */
    public function created(TopUp $topUp)
    {
        //notify admin
        if (App::environment() != 'testing')
            TelegramService::sendAdminMessage("Received a new top up from user id of " . $topUp->user->id . " <a href='" . $topUp->getApproveLink() . "'>here</a>");
    }

    /**
     * Handle the TopUp "updated" event.
     *
     * @param  \App\Models\TopUp  $topUp
     * @return void
     */
    public function updated(TopUp $topUp)
    {
        if ($topUp->status == 2) {
            //top_ups_status 2, approve
            $topUp->user->notify(__("messages.Top Up has been approved"));
            $topUp->user->increasePoint(Point::find(2), $topUp->amount, 'top up approved', $topUp, referrable: true);
        }
    }

    /**
     * Handle the TopUp "deleted" event.
     *
     * @param  \App\Models\TopUp  $topUp
     * @return void
     */
    public function deleted(TopUp $topUp)
    {
        //
    }

    /**
     * Handle the TopUp "restored" event.
     *
     * @param  \App\Models\TopUp  $topUp
     * @return void
     */
    public function restored(TopUp $topUp)
    {
        //
    }

    /**
     * Handle the TopUp "force deleted" event.
     *
     * @param  \App\Models\TopUp  $topUp
     * @return void
     */
    public function forceDeleted(TopUp $topUp)
    {
        //
    }
}
