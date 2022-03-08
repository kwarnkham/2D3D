<?php

namespace App\Http\Controllers;

use App\Enums\ResponseStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TwoDigitController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'numbers' => ['required', 'array'],
            'numbers.*.number' => ['required', 'numeric'],
            'numbers.*.amount' => ['required', 'numeric'],
            'point_id' => ['required', 'exists:points,id']
        ]);
        $user = $request->user();
        $remainingBalance = $user->getBalanceByPointId($data['point_id']) - intval(collect($data['numbers'])->reduce(fn ($carry, $value) => $carry + $value['amount'], 0));
        if ($remainingBalance < 0) abort(ResponseStatus::BAD_REQUEST->value, 'Balance is not enough');
        $twoDigits = collect($data['numbers'])->map(fn ($value) => [
            'number' => $value['number'],
            'amount' => $value['amount'],
            'point_id' => $data['point_id']
        ])->toArray();
        $result = null;
        DB::transaction(function () use ($twoDigits, $user, &$result, $data, $remainingBalance) {
            $result = $user->twoDigits()->createMany($twoDigits);
            if ($result) $user->points()->updateExistingPivot($data['point_id'], [
                'balance' => $remainingBalance,
            ]);
        });

        return response()->json(['result' => $result, 'user' => $user->load(['points'])]);
    }
}
