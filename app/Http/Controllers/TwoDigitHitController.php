<?php

namespace App\Http\Controllers;

use App\Enums\ResponseStatus;
use App\Models\PointLog;
use App\Models\TwoDigit;
use App\Models\TwoDigitHit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TwoDigitHitController extends Controller
{
    public function store(Request $request)
    {
        Gate::authorize('admin');
        $data = $request->validate([
            'number' => ['required', 'numeric', 'digits_between:1,2'],
            'rate' => ['required', 'numeric'],
            'day' => ['required', 'string', 'date'],
            'morning' => ['required', 'boolean']
        ]);
        if (TwoDigitHit::where('day', $data['day'])->where('morning', $data['morning'])->exists()) abort(ResponseStatus::BAD_REQUEST->value, "Already settled for " . $data['day'] . ($data['morning'] ? " morning" : " evening"));
        return response()->json(DB::transaction(function () use ($data) {
            $twoDigitHit =  TwoDigitHit::create($data);
            $twoDigitHit->settle();
            return $twoDigitHit;
        }), ResponseStatus::CREATED->value);
    }

    public function find(Request $request, TwoDigitHit $twoDigitHit, PointLog $pointLog)
    {
        return [$twoDigitHit, $pointLog];
        $twoDigit = $twoDigitHit->twoDigits()->where('id', explode(",", $pointLog->note)[0])->first();
        $twoDigitHit->twoDigit = $twoDigit->load(['point']);
        return response()->json($twoDigitHit);
    }

    public function index(Request $request)
    {
        return response()->json(Cache::rememberForever('twoDigitHits', function () {
            return TwoDigitHit::all();
        }));
    }
}
