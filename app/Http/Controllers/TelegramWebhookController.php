<?php

namespace App\Http\Controllers;

use App\Models\AccountProvider;
use App\Models\User;
use App\Models\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                if (UserProvider::where('provider_id', $request->message['from']['id'])->first()) return;
                DB::beginTransaction();
                $user = User::create([
                    'name' => 't' . $request->message['from']['username'],
                    'password' => bcrypt('000000')
                ]);
                $accountProvider = AccountProvider::where('name', 'telegram')->first();
                if (!$accountProvider) $accountProvider = AccountProvider::create(['name' => 'telegram']);
                UserProvider::create([
                    'user_id' => $user->id,
                    'account_provider_id' => $accountProvider->id,
                    'provider_id' => $request->message['from']['id'],
                    'username' => $request->message['from']['username'],
                    'sent_at' => $request->message['date']
                ]);
                DB::commit();
                break;
            default:
                # code...
                break;
        }
    }
}
