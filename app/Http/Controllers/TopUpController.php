<?php

namespace App\Http\Controllers;

use App\Models\TopUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $topUp = null;

        DB::transaction(function () use ($data, &$topUp) {
            $topUp = TopUp::create($data);
            $topUp->savePictures($data['pictures']);
        });

        return response()->json($topUp);
    }
}
