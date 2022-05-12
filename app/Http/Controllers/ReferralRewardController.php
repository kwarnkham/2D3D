<?php

namespace App\Http\Controllers;

use App\Enums\ResponseStatus;
use App\Models\ReferralReward;
use Illuminate\Http\Request;

class ReferralRewardController extends Controller
{
    public function find(Request $request, ReferralReward $referralReward)
    {
        abort_if(!$request->user()->isAdmin() && $referralReward->referrer_id != $request->user()->id, ResponseStatus::NOT_FOUND->value, __("messages.2d ticket"));

        return response()->json($referralReward->load(ReferralReward::RS));
    }
}
