<?php

namespace App\Http\Controllers;

use App\Models\AppVersion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::channel('telegram')->info(json_encode($request->all()));
        $request->validate([
            'message' => ['required'],
            'message.from.id' => ['required'],
            'message.text' => ['required'],
            'message.date' => ['required']
        ]);
        list($user, $password) = User::summon($request);
        switch (strtolower($request->message['text'])) {
            case 'english':
                $user->setLocale('en');
                break;
            case 'မြန်မာ':
                $user->setLocale('my');
                break;
            default:
                if (in_array($user->preferredLocale(), ['en', 'my'])) App::setLocale($user->preferredLocale());
                break;
        }


        $username = $user->name;
        $appName = env('APP_NAME');
        $apkUrl = AppVersion::apkUrl();
        $appClient = env("APP_CLIENT_URL");
        $message = __("messages.default message", compact('appName', 'apkUrl', 'appClient'));
        $starterMessage = $message . __("messages.The followings are username and password.");
        switch (strtolower($request->message['text'])) {
            case 'application':
            case 'အပလီကေးရှင်း':

                $message = __("messages.get application", compact('apkUrl'));
                break;
            case 'hi':
            case 'account':
            case '/start':
            case 'english':
            case 'မြန်မာ':
                if ($password) $message = $starterMessage;
                else $message = $message . __("messages.is your username", compact('username'));
                break;
            case strtolower(__("messages.forgot password")):
                if ($password) {
                    $message = $starterMessage;
                    break;
                }
                if ($user->hasRecentPasswordChange()) {
                    $message = __("messages.To reset password again, you have to wait for 24 hours after changing password");
                } else {
                    $url = URL::temporarySignedRoute(
                        'resetPassword',
                        now()->addMinutes(30),
                        ['user_id' => $user->id],
                    );
                    parse_str(parse_url($url)['query'], $query);
                    $clientUrl = env("APP_CLIENT_URL") . "/reset-password/$query[expires]/$query[user_id]/$query[signature]";
                    $message = __("messages.password change warning", compact('clientUrl'));
                }
                break;
            case strtolower(__("messages.help")):
                $message = "help ";
                break;
            default:
                if ($password) $message = $starterMessage;
                else
                    $message = null;
                break;
        }
        if ($message) {
            try {
                if ($password)
                    $user->notify([$message, $username, $password]);
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
}
