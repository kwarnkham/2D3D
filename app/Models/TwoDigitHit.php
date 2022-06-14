<?php

namespace App\Models;

use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TwoDigitHit extends AppModel
{
    use HasFactory;

    protected $appends = ['created_time'];

    public function twoDigits()
    {
        return $this->hasMany(TwoDigit::class);
    }

    public static function checkDay(Carbon $runTime = null)
    {
        if (!$runTime) $runTime = now();
        $today = (clone $runTime)->startOfDay();
        if (in_array($today, array_map(
            fn ($day) => new Carbon($day),
            TwoDigit::CLOSED_DAYS
        ))) {
            return false;
        } else if ($runTime->isDayOfWeek(Carbon::SATURDAY) || $runTime->isDayOfWeek(Carbon::SUNDAY)) {
            return false;
        } else {
            return true;
        }
    }

    public static function confirmResult(array $data)
    {
        if (!App::isLocale('en')) App::setLocale('en');
        if (!TwoDigitHit::where('day', $data['day'])->where('morning', $data['morning'])->exists()) {
            $data['day'] = new Carbon($data['day']);
            $twoDigitHit = TwoDigitHit::create($data);
            Log::channel('two-digit')->info(($data['morning'] ? "Morning" : "Evening") . " result is " . $data['number']);
            TelegramService::sendAdminMessage('Finished settled for the following.');
            TelegramService::sendAdminMessage(json_encode($twoDigitHit));
        } else {
            TelegramService::sendAdminMessage('Already settled for the following.');
            TelegramService::sendAdminMessage(json_encode($data));
        }
    }

    public function settle()
    {
        return DB::transaction(function () {
            if ($this->morning) {
                $morningStartTime = (new Carbon($this->day))->subDay()->addSeconds(TwoDigit::EVENING_DURATION + 3600 - 59);
                $morningEndTime = (new Carbon($this->day))->addSeconds(TwoDigit::MORNING_DURATION);
                $builder = TwoDigit::where('created_at', '>=', $morningStartTime)
                    ->where('created_at', '<=', $morningEndTime)
                    ->whereNull('settled_at');
            } else {
                $eveningStartTime = (new Carbon($this->day))->addSeconds(TwoDigit::MORNING_DURATION + 3600 - 59);
                $eveningEndTime = (new Carbon($this->day))->addSeconds(TwoDigit::EVENING_DURATION);
                $builder = TwoDigit::where('created_at', '>=', $eveningStartTime)
                    ->where('created_at', '<=', $eveningEndTime)
                    ->whereNull('settled_at');
            }
            $twoDigitUpdateData = ['two_digit_hit_id' => $this->id];
            $jackpotNumber = JackpotNumber::whereNull('hit_at')->orderBy('id', 'desc')->first();

            //hit jackpot
            if ($jackpotNumber && $this->number == $jackpotNumber->number) {
                $shared = (clone $builder)->where('number', $this->number)->count();

                if ($shared > 0) {
                    $totalBet = (clone $builder)->where('number', $this->number)->sum('amount');
                    $dupeKeysData = (clone $builder)->where('number', $this->number)->with('user')->get()->map(
                        function ($val) use ($totalBet) {
                            return [
                                'user_id' => $val->user->id,
                                'shared_percentage' => ($val->amount / $totalBet) * 100
                            ];
                        }
                    );
                    $uniqueKeysData = array();
                    foreach ($dupeKeysData as $value) {
                        if (isset($uniqueKeysData[$value['user_id']])) {
                            $uniqueKeysData[$value['user_id']] += $value['shared_percentage'];
                        } else {
                            $uniqueKeysData[$value['user_id']] = $value['shared_percentage'];
                        }
                    }
                    $amount = Jackpot::getJackpot(false);
                    $uniqueKeysData = collect($uniqueKeysData)->map(
                        function ($item) use ($amount) {
                            return ['reward' => floor(($item * $amount) / 100)];
                        }
                    )->toArray();

                    $sharedAmount = floor($amount / $shared);
                    $jackpotReward = JackpotReward::create([
                        'amount' => $amount,
                        'shared_amount' => $sharedAmount,
                        'jackpot_number_id' => $jackpotNumber->id
                    ]);
                    $jackpotReward->users()->attach($uniqueKeysData);
                    $twoDigitUpdateData['jackpot_reward_id'] = $jackpotReward->id;
                    $jackpotNumber->hit_at = now();
                    $jackpotNumber->save();
                    JackpotNumber::create([
                        'number' => $jackpotNumber->number == 99 ? 0 : $jackpotNumber->number + 11
                    ]);
                    Jackpot::whereIn('id', Jackpot::effectiveQuery()->pluck('id')->toArray())
                        ->update(['status' => 2, 'jackpot_reward_id' => $jackpotReward->id]);
                }
            };

            (clone $builder)->where('number', $this->number)->update($twoDigitUpdateData);


            $builder->update(['settled_at' => now()]);
            foreach ($this->twoDigits as $twoDigit) {
                $twoDigit->processPrize();
            }
            TwoDigit::processJackpot($this);
        });
    }
}
