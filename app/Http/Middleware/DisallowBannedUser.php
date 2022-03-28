<?php

namespace App\Http\Middleware;

use App\Enums\ResponseStatus;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class DisallowBannedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user)
            $user = User::where('name', $request->name)->first();
        if ($user)
            abort_if($user->isBanned(), ResponseStatus::UNAUTHORIZED->value, "The account has been banned");
        return $next($request);
    }
}
