<?php

namespace App\Observers;

use App\Models\Point;
use App\Models\TopUp;

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
        //
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
            $topUp->user->increasePoint(Point::find(2), $topUp->amount, 'Approved Top Up');
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
