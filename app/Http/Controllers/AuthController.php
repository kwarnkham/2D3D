<?php

namespace App\Http\Controllers;

use App\Enums\ResponseStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'password' => ['required']
        ]);
        $user = User::where('name', $request->name)->first();
        if (!$user) abort(ResponseStatus::UNAUTHORIZED->value, 'User not fount');
        if (!Hash::check($request->password, $user->password)) abort(ResponseStatus::UNAUTHORIZED->value, 'Password is incorrect');
        $user->tokens()->delete();
        $token = $user->createToken("");
        return response()->json(['token' => $token->plainTextToken, 'user' => $user]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => ['required'],
            'new_password' => ['required', 'confirmed', 'min:6']
        ]);
        $user = $request->user();
        if (!Hash::check($request->password, $user->password)) {
            abort(ResponseStatus::UNAUTHORIZED->value, "Incorrect password");
        }

        $user->password = $request->new_password;
        $user->save();
        return response()->json('success');
    }
}
