<?php

namespace App\Observers;

use App\Models\TwoDigitHit;

class TwoDigitHitObserver
{
    /**
     * Handle the TwoDigitHit "created" event.
     *
     * @param  \App\Models\TwoDigitHit  $twoDigitHit
     * @return void
     */
    public function created(TwoDigitHit $twoDigitHit)
    {
    }

    /**
     * Handle the TwoDigitHit "updated" event.
     *
     * @param  \App\Models\TwoDigitHit  $twoDigitHit
     * @return void
     */
    public function updated(TwoDigitHit $twoDigitHit)
    {
        //
    }

    /**
     * Handle the TwoDigitHit "deleted" event.
     *
     * @param  \App\Models\TwoDigitHit  $twoDigitHit
     * @return void
     */
    public function deleted(TwoDigitHit $twoDigitHit)
    {
        //
    }

    /**
     * Handle the TwoDigitHit "restored" event.
     *
     * @param  \App\Models\TwoDigitHit  $twoDigitHit
     * @return void
     */
    public function restored(TwoDigitHit $twoDigitHit)
    {
        //
    }

    /**
     * Handle the TwoDigitHit "force deleted" event.
     *
     * @param  \App\Models\TwoDigitHit  $twoDigitHit
     * @return void
     */
    public function forceDeleted(TwoDigitHit $twoDigitHit)
    {
        //
    }
}
