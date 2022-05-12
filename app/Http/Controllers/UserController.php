<?php

namespace App\Http\Controllers;

use App\Enums\ResponseStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'status' => ['in:banned,normal'],
            'order_in' => ['in:desc,asc'],
            'name' => ['string'],
            'without_admin' => ['boolean'],
            "without_user" => ['boolean'],
        ]);
        $query = User::with(User::RS)
            ->filter($request->only(['status', 'order_in', 'name', 'without_admin', 'without_user']));
        return response()->json(
            $query->paginate($request->per_page ?? 10)
        );
    }

    public function ban(Request $request, User $user)
    {
        Gate::authorize('admin');
        $user->banned_at = now();
        $user->save();
        return response()->json($user->load(User::RS));
    }

    public function unBan(Request $request, User $user)
    {
        Gate::authorize('admin');
        $user->banned_at = null;
        $user->save();
        return response()->json($user->load(User::RS));
    }

    public function setLocale(Request $request)
    {
        $request->validate([
            'locale' => ['required', 'in:en-US,my']
        ]);
        $supported = [
            'en-US' => 'en',
            'my' => 'my'
        ];
        $request->user()->setLocale($supported[$request->locale]);
        return response()->json('ok');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'unique:users,name', 'alpha_num'],
            'password' => ['required']
        ]);
        abort_if(!Hash::check($request->password, $user->password), ResponseStatus::UNAUTHORIZED->value, __("messages.Incorrect password"));
        $user->name = $request->name;
        $user->save();
        return response()->json($user->load(User::RS));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load(User::RS));
    }
}
