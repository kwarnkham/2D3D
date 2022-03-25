<?php

namespace App\Http\Controllers;

use App\Enums\ResponseStatus;
use App\Models\Point;
use App\Models\TwoDigit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TwoDigitController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'numbers' => ['required', 'array'],
            'numbers.*.number' => ['required', 'numeric', 'digits_between:1,2'],
            'numbers.*.amount' => ['required', 'numeric', 'min:100'],
            'point_id' => ['required', 'exists:points,id']
        ]);
        TwoDigit::checkTime();

        $user = $request->user();
        $point = Point::find($data['point_id']);
        $totalAmount = intval(collect($data['numbers'])->reduce(fn ($carry, $value) => $carry + $value['amount'], 0));
        $remainingBalance = $user->getBalanceByPoint($point) - $totalAmount;
        if ($remainingBalance < 0) abort(ResponseStatus::BAD_REQUEST->value, 'Balance is not enough');
        $twoDigits = collect($data['numbers'])->map(fn ($value) => [
            'number' => $value['number'],
            'amount' => $value['amount'],
            'point_id' => $data['point_id']
        ])->toArray();

        return response()->json([
            'result' => DB::transaction(
                fn () => $user->twoDigits()->createMany($twoDigits)
            ),
            'user' => $user->load(User::RS)
        ]);
    }

    public function index(Request $request)
    {
        $query = TwoDigit::with(TwoDigit::RS)->orderBy('id', 'desc');
        if (!$request->user()->isAdmin()) $query->of($request->user());
        return response()->json($query->paginate(perPage: 30));
    }

    public function find(Request $request, TwoDigit $twoDigit)
    {
        $user = $request->user();
        if ($twoDigit->user->id != $user->id && !$user->isAdmin()) abort(ResponseStatus::NOT_FOUND->value);
        return response()->json($twoDigit->load(['point']));
    }
}
