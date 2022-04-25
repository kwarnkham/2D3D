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
        $starterMessage = "Click <a href='https://google.com'>here</a> to download the app. IOS is not supported yet. For ios users, please click <a href='https://google.com'>here</a> to use the web application. Here is your account. Username is '$username'. Please change your password immediately after login. Your password is the following message.";
        $passwordMessage = "'$password'";
        switch ($request->message['text']) {
            case 'hi':
            case 'account':
            case '/start':
                if ($password) $message = $starterMessage;
                break;
            case 'Forgot Password':
                if ($password) {
                    $message = $starterMessage;
                    break;
                }
                if ($user->hasRecentPasswordChange()) {
                    $message = "To reset password again, you have to wait for 24 hours after changing password";
                } else {
                    $url = URL::temporarySignedRoute(
                        'resetPassword',
                        now()->addMinutes(30),
                        ['user_id' => $user->id],
                    );
                    parse_str(parse_url($url)['query'], $query);
                    $clientUrl = env("APP_CLIENT_URL") . "/reset-password/$query[expires]/$query[user_id]/$query[signature]";
                    $message = "You can change or reset your password only one time in 24 hours and withdraw is blocked for 24 hours after chaniging the password for security reasons. Click <a href='$clientUrl'>here</a> to reset the password.";
                }
                break;
            default:
                break;
        }

        try {
            if ($password)
                $user->notify([$message, $passwordMessage]);
            else
                $user->notify($message);
        } catch (\Throwable $th) {
            if ($password && str()->contains($message, $password)) {
                $user->reverseResitration();
            }
            throw $th;
        }
    }
}
