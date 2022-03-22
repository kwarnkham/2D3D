<?php

namespace App\Http\Controllers;

use App\Enums\ResponseStatus;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class WithdrawController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'amount' => ['required', 'lte:' . $user->mmk()->pivot->balance],
            'account' => ['required'],
            'username' => ['required'],
            'payment_id' => ['exists:payments,id']
        ]);
        $data['point_id'] = 2;
        $user->withdraws()->create($data);
        return response()->json($user->load(User::RS));
    }

    public function index(Request $request)
    {
        $request->validate([
            'status' => ['in:1,2,3,4'],
            'order_in' => ['in:desc,asc']
        ]);
        return response()->json(
            Withdraw::with(Withdraw::RS)
                ->filter($request->only(['status', 'order_in']))
                ->paginate($request->per_page ?? 10)
        );
    }

    public function find(Request $request, Withdraw $withdraw)
    {
        return response()->json($withdraw->load(Withdraw::RS));
    }

    public function approve(Request $request, Withdraw $withdraw)
    {
        $data = $request->validate([
            'pictures' => ['required', 'array'],
            'pictures.*' => ['image']
        ]);
        Gate::authorize('admin');
        if (!in_array($withdraw->status, ['1', '4'])) abort(ResponseStatus::BAD_REQUEST->value, "Can only approve a pending or drafted Withdraw");
        return response()->json(DB::transaction(function () use ($data, &$withdraw) {
            $withdraw->status = 2;
            $withdraw->save();
            $withdraw->savePictures($data['pictures']);
            return $withdraw->load(Withdraw::RS);
        }));
    }

    public function draft(Request $request, Withdraw $withdraw)
    {
        Gate::authorize('admin');
        if (!in_array($withdraw->status, ['1'])) abort(ResponseStatus::BAD_REQUEST->value, "Can only draft a pending Withdraw");
        $withdraw->status = 4;
        $withdraw->save();
        return response()->json($withdraw->load(Withdraw::RS));
    }

    public function deny(Request $request, Withdraw $withdraw)
    {
        Gate::authorize('admin');
        if (!in_array($withdraw->status, ['1', '4'])) abort(ResponseStatus::BAD_REQUEST->value, "Can only deny a pending or drafted Withdraw");
        $withdraw->status = 3;
        $withdraw->save();
        return response()->json($withdraw->load(Withdraw::RS));
    }

    public function cancel(Request $request, Withdraw $withdraw)
    {
        Gate::authorize('cancel-withdraw', $withdraw);
        if (!in_array($withdraw->status, ['1', '4'])) abort(ResponseStatus::BAD_REQUEST->value, "Can only cancel a pending or drafted Withdraw");
        $withdraw->status = 5;
        $withdraw->save();
        return response()->json($withdraw->load(Withdraw::RS));
    }
}
