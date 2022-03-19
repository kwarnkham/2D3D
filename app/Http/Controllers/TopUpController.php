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
        return response()->json(TopUp::with(TopUp::RS)->paginate());
    }

    public function approve(Request $request, TopUp $topUp)
    {
        Gate::authorize('admin');
        $request->validate([
            'status' => ['required', 'in:2,3,4']
        ]);
        if ($topUp->status != '1') abort(ResponseStatus::BAD_REQUEST->value, "Can only approve a pending Top UP");
        $topUp->status = $request->status;
        $topUp->save();
        return response()->json($topUp->load(TopUp::RS));
    }
}
