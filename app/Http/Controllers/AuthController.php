<?php

namespace App\Http\Controllers;

use App\Enums\ResponseStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        if (!$user) abort(ResponseStatus::UNAUTHORIZED->value, 'User not found');
        if (!Hash::check($request->password, $user->password)) abort(ResponseStatus::UNAUTHORIZED->value, 'Password is incorrect');
        $user->tokens()->delete();
        $token = $user->createToken("");
        return response()->json(['token' => $token->plainTextToken, 'user' => $user->load(User::RS)]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => ['required'],
            'new_password' => ['required', 'confirmed', 'min:6']
        ]);
        $user = $request->user();

        abort_if(
            $user->hasRecentPasswordChange(),
            ResponseStatus::BAD_REQUEST->value,
            __("messages.Password can be changed only one time in 24 hours")
        );

        if (!Hash::check($request->password, $user->password)) {
            abort(ResponseStatus::UNAUTHORIZED->value, __("messages.Incorrect password"));
        }

        DB::transaction(function () use ($user, $request) {
            $user->password = $request->new_password;
            $user->save();
            //password_changes_type 1, changePassword
            $user->passwordChanges()->create(['type' => 1]);
        });

        return response()->json($user->refresh()->load(User::RS));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed']
        ]);
        DB::transaction(function () use ($request) {
            $user = User::findOrFail($request->user_id);
            $user->password = $request->password;
            $user->tokens()->delete();
            //password_changes_type 2, reset password
            $user->passwordChanges()->create([
                'type' => 2
            ]);
            $user->save();
        });
        return response()->json(true);
    }
}
