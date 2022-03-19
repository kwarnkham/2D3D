<?php

namespace App\Http\Controllers;

use App\Enums\ResponseStatus;
use App\Models\TopUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TopUpController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric'],
            'payment_id' => ['required', 'exists:payments,id'],
            'payment_username' => ['required'],
            'pictures' => 'array',
            'pictures.*' => 'image',
        ]);
        $data['user_id'] = $request->user()->id;

        return response()->json(DB::transaction(function () use ($data, &$topUp) {
            $topUp = TopUp::create($data);
            $topUp->savePictures($data['pictures']);
            return $topUp;
        }));
    }

    public function index(Request $request)
    {
        $request->validate(['status' => ['in:1,2,3,4']]);
        return response()->json(TopUp::with(TopUp::RS)->filter($request->only(['status']))->paginate($request->per_page ?? 15));
    }

    public function approve(Request $request, TopUp $topUp)
    {
        Gate::authorize('admin');
        if (!in_array($topUp->status, ['1', '4'])) abort(ResponseStatus::BAD_REQUEST->value, "Can only approve a pending or drafted Top Up");
        $topUp->status = 2;
        $topUp->save();
        return response()->json($topUp->load(TopUp::RS));
    }

    public function draft(Request $request, TopUp $topUp)
    {
        Gate::authorize('admin');
        if (!in_array($topUp->status, ['1'])) abort(ResponseStatus::BAD_REQUEST->value, "Can only approve a pending Top Up");
        $topUp->status = 4;
        $topUp->save();
        return response()->json($topUp->load(TopUp::RS));
    }

    public function deny(Request $request, TopUp $topUp)
    {
        Gate::authorize('admin');
        if (!in_array($topUp->status, ['1', '4'])) abort(ResponseStatus::BAD_REQUEST->value, "Can only deny a pending or drafted Top Up");
        $topUp->status = 3;
        $topUp->save();
        return response()->json($topUp->load(TopUp::RS));
    }
}
