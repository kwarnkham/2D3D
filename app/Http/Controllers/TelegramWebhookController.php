<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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

        switch ($request->message['text']) {
            case 'hi':
            case 'account':
            case '/start':
                $user = User::register($request);
                $username = $user->name;
                $password = '000000';
                $message = "Click <a href='https://google.com'>here</a> to download the app. IOS is not supported yet. For ios users, please click <a href='https://google.com'>here</a> to use the web application. Here is your account. Username '$username'. Password '$password'. Please change your password immediately after login.";
                $user->notify($message);
                break;
            default:
                # code...
                break;
        }
    }
}
