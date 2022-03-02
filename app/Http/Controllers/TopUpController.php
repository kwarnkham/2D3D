<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
    }
}
