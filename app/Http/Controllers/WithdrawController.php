<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
}
