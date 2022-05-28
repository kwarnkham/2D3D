<?php

namespace App\Http\Controllers;

use App\Enums\ResponseStatus;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    public static function current()
    {
        return response()->json(AppSetting::current());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pool_amount' => ['required', 'numeric'],
            'jackpot_rate' => ['required', 'numeric'],
            'referral_rate' => ['required', 'numeric'],
            'rate' => ['required', 'numeric'],
        ]);
        $current = AppSetting::current();
        $same = true;
        foreach ($data as $key => $value) {
            if ($value != $current[$key]) {
                $same = false;
                break;
            }
        }
        if ($same) $appSetting = $current;
        else  $appSetting = AppSetting::create($data);
        abort_unless($appSetting, ResponseStatus::SERVER_ERROR->value, 'Cannot update');
        return response()->json($appSetting);
    }
}
