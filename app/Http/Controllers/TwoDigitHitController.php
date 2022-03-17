<?php

namespace App\Http\Controllers;

use App\Enums\ResponseStatus;
use App\Models\TwoDigitHit;
use Illuminate\Http\Request;
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
        return response()->json(TwoDigitHit::create($data));
    }
}
