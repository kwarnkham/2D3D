<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $request->validate([
            'message' => ['required'],
            'message.from.id' => ['required'],
            'message.from.username' => ['required'],
            'message.text' => ['required'],
            'message.date' => ['required']
        ]);
        list($user, $password) = User::summon($request);
        $username = $user->name;
        $message = "Click <a href='https://google.com'>here</a> to download the app. IOS is not supported yet. For ios users,please click <a href='https://google.com'>here</a> to use the web application. Your account username is '$username'";
        switch ($request->message['text']) {
            case 'hi':
            case 'account':
            case '/start':
                if ($password)
                    $message = "Click <a href='https://google.com'>here</a> to download the app. IOS is not supported yet. For ios users, please click <a href='https://google.com'>here</a> to use the web application. Here is your account. Username '$username'. Password '$password'. Please change your password immediately after login.";
                break;
            case 'Forgot Password':
                $url = URL::temporarySignedRoute(
                    'resetPassword',
                    now()->addMinutes(30),
                    ['user_id' => $user->id],
                );
                parse_str(parse_url($url)['query'], $query);
                $clientUrl = env("APP_CLIENT_URL") . "/reset-password/$query[expires]/$query[user_id]/$query[signature]";
                $message = "You can change or reset your password only one time in 24 hours and withdraw is blocked for 24 hours after chaniging the password for security reasons. Click <a href='$clientUrl'>here</a> to reset the password.";
                break;
            default:
                break;
        }
        $user->notify($message);
    }
}
