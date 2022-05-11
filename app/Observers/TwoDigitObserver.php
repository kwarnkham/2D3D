<?php

namespace App\Observers;

use App\Models\TwoDigit;

class TwoDigitObserver
{
    /**
     * Handle the TwoDigit "created" event.
     *
     * @param  \App\Models\TwoDigit  $twoDigit
     * @return void
     */
    public function created(TwoDigit $twoDigit)
    {
        $twoDigit->user->decreasePoint($twoDigit->point, $twoDigit->amount, '2d ticket', $twoDigit);
    }

    /**
     * Handle the TwoDigit "updated" event.
     *
     * @param  \App\Models\TwoDigit  $twoDigit
     * @return void
     */
    public function updated(TwoDigit $twoDigit)
    {
        //
    }

    /**
     * Handle the TwoDigit "deleted" event.
     *
     * @param  \App\Models\TwoDigit  $twoDigit
     * @return void
     */
    public function deleted(TwoDigit $twoDigit)
    {
        //
    }

    /**
     * Handle the TwoDigit "restored" event.
     *
     * @param  \App\Models\TwoDigit  $twoDigit
     * @return void
     */
    public function restored(TwoDigit $twoDigit)
    {
        //
    }

    /**
     * Handle the TwoDigit "force deleted" event.
     *
     * @param  \App\Models\TwoDigit  $twoDigit
     * @return void
     */
    public function forceDeleted(TwoDigit $twoDigit)
    {
        //
    }
}
