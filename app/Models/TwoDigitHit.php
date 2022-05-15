<?php

namespace App\Models;

use App\Enums\ResponseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TwoDigitHit extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function twoDigits()
    {
        return $this->hasMany(TwoDigit::class);
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
            $jackPotNumber = JackPotNumber::whereNull('hit_at')->orderBy('id', 'desc')->first();

            //hit jackpot
            if ($jackPotNumber && $this->number == $jackPotNumber->number) {
                $shared = (clone $builder)->where('number', $this->number)->count();
                if ($shared > 0) {
                    $amount = JackPot::getJackPot(false);
                    $sharedAmount = floor($amount / $shared);
                    $jackPotReward = JackPotReward::create([
                        'amount' => $amount,
                        'shared_amount' => $sharedAmount,
                        'jack_pot_number_id' => $jackPotNumber->id
                    ]);
                    $twoDigitUpdateData['jack_pot_reward_id'] = $jackPotReward->id;
                    $jackPotNumber->hit_at = now();
                    $jackPotNumber->save();
                    JackPotNumber::create([
                        'number' => $jackPotNumber->number == 99 ? 0 : $jackPotNumber->number + 1
                    ]);
                    JackPot::whereIn('id', JackPot::effectiveQuery()->pluck('id')->toArray())
                        ->update(['status' => 2, 'jack_pot_reward_id' => $jackPotReward->id]);
                }
            };

            (clone $builder)->where('number', $this->number)->update($twoDigitUpdateData);


            $builder->update(['settled_at' => now()]);
            foreach ($this->twoDigits as $twoDigit) {
                $twoDigit->processPrize();
            }
            TwoDigit::processJackPot($this);
        });
    }
}
