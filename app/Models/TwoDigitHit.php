<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class TwoDigitHit extends AppModel
{
    use HasFactory;


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

    public function settle()
    {
        return DB::transaction(function () {
            if ($this->morning) {
                $morningStartTime = (new Carbon($this->day))->subDay()->addSeconds(TwoDigit::EVENING_DURATION + 3600 - 59);
                $morningEndTime = (new Carbon($this->day))->addSeconds(TwoDigit::MORNING_DURATION);
                // if (now()->greaterThan(today()->addHours(9)->addMinutes(31))) {
                //     $morningStartTime->addDay();
                //     $morningEndTime->addDay();
                // }
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
                    $amount = Jackpot::getJackpot(false);
                    $sharedAmount = floor($amount / $shared);
                    $jackpotReward = JackpotReward::create([
                        'amount' => $amount,
                        'shared_amount' => $sharedAmount,
                        'jackpot_number_id' => $jackpotNumber->id
                    ]);
                    $twoDigitUpdateData['jackpot_reward_id'] = $jackpotReward->id;
                    $jackpotNumber->hit_at = now();
                    $jackpotNumber->save();
                    JackpotNumber::create([
                        'number' => $jackpotNumber->number == 99 ? 0 : $jackpotNumber->number + 1
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
