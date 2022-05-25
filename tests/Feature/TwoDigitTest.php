<?php

namespace Tests\Feature;

use App\Models\Jackpot;
use App\Models\JackpotNumber;
use App\Models\JackpotReward;
use App\Models\Point;
use App\Models\PointLog;
use App\Models\TopUp;
use App\Models\TwoDigit;
use App\Models\TwoDigitHit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

use function PHPUnit\Framework\assertTrue;

class TwoDigitTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_two_digit()
    {
        $this->withoutExceptionHandling();
        Point::create(['name' => 'Lucky Hi']);
        Point::create(['name' => 'MMK']);
        \App\Models\AppSetting::create([
            'pool_amount' => '1000000', 'config' => [
                'jackpot_rate' => '0.1',
                'referral_rate' => '0.05'
            ]
        ]);
        \App\Models\Payment::create([
            'name' => 'KBZPay', 'mm_name' => 'ကေပေး', 'type' => 1, 'number' => '09123123123', 'account_name' => 'moon'
        ]);
        $user = User::create(['name' => 'moon1', 'password' => '123123']);
        $response = $this->actingAs($user)->postJson('api/top-up', [
            'amount' => 10000,
            'payment_id' => 1,
            'pictures' => [UploadedFile::fake()->image('avatar.jpg')]
        ]);
        $response->assertCreated();

        \App\Models\Role::create(['name' => 'admin']);
        \App\Models\JackpotNumber::create(['number' => 0]);
        Artisan::call('make:admin moon ninjamoon');
        $admin = User::where('name', 'moon')->first();
        $response = $this->actingAs($admin)->postJson('api/top-up/approve/1', [
            'picture' => UploadedFile::fake()->image('avatar.jpg')
        ]);
        $response->assertOk();

        $response = $this->actingAs($user)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 0, 'amount' => 100],
                ['number' => 1, 'amount' => 150]
            ],
            'point_id' => 2
        ]);

        $response->assertStatus(201);

        $response = $this->actingAs($admin)->postJson('api/two-digit-hit', [
            'number' => '99',
            'rate' => 10,
            'day' => '2022/05/15',
            'morning' => false
        ]);

        $response->assertStatus(201);
        assertTrue(Jackpot::getJackpot(false) == 25);
        assertTrue(TwoDigitHit::find(1)->update(['day' => '2022/05/14']) == 1);
        $user2 = User::create(['name' => 'moon2', 'password' => '123123']);
        $response = $this->actingAs($user2)->postJson('api/top-up', [
            'amount' => 10000,
            'payment_id' => 1,
            'pictures' => [UploadedFile::fake()->image('avatar.jpg')]
        ]);
        $response->assertCreated();
        $response = $this->actingAs($admin)->postJson('api/top-up/approve/2', [
            'picture' => UploadedFile::fake()->image('avatar.jpg')
        ]);
        $response->assertOk();

        $response = $this->actingAs($user)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 0, 'amount' => 100],
                ['number' => 1, 'amount' => 100]
            ],
            'point_id' => 2
        ]);

        $response->assertStatus(201);
        $response = $this->actingAs($user2)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 0, 'amount' => 100],
                ['number' => 1, 'amount' => 100]
            ],
            'point_id' => 2
        ]);

        $response->assertStatus(201);

        $response = $this->actingAs($admin)->postJson('api/two-digit-hit', [
            'number' => '0',
            'rate' => 10,
            'day' => '2022/05/15',
            'morning' => false
        ]);

        $response->assertStatus(201);

        assertTrue(JackpotReward::find(1)->shared_amount == floor(Jackpot::where('status', 2)->pluck('amount')->sum() / 2));
        assertTrue(PointLog::where('user_id', $user->id)->orderBy('id', 'desc')->first()->amount == JackpotReward::find(1)->shared_amount);
        assertTrue(PointLog::where('user_id', $user2->id)->orderBy('id', 'desc')->first()->amount == JackpotReward::find(1)->shared_amount);
        assertTrue(JackpotNumber::whereNotNull('hit_at')->first()->number == 0);
        assertTrue(Jackpot::where('status', 2)->count() == 2);
        assertTrue(Jackpot::where('status', 2)->first()->jack_pot_reward_id == 1);

        assertTrue(TwoDigit::where('number', 0)->orderBy('id', 'desc')->first()->jack_pot_reward_id == 1);
        assertTrue(TwoDigit::where('number', 0)->orderBy('id', 'desc')->first()->two_digit_hit_id == 2);
        assertTrue(PointLog::where('note', 'jackpot prize')->count() == 2);
        assertTrue(PointLog::where('note', '2d prize')->count() == 2);
        assertTrue(JackpotNumber::orderBy('id', 'desc')->first()->number == 1);
    }

    public function test_2d_time()
    {
        for ($i = 0; $i < 60 * 60 * 24 * 365; $i++) {
            $time = today()->startOfYear()->addSeconds($i);
            Log::channel('debug')->info($time->format("Y-m-d D H:i:s A"));
            $morningStart = (clone $time)->startOfDay()->addHours(10)->addMinutes(30);
            $morningLast = (clone $time)->startOfDay()->addHours(5)->addSeconds(59);
            $eveningStart = (clone $time)->startOfDay()->addHours(6);
            $eveningLast = (clone $time)->startOfDay()->addHours(9)->addMinutes(30)->addSeconds(59);

            if (TwoDigit::checkDay($time)) {
                if ($time->lessThanOrEqualTo($morningLast))
                    $this->assertTrue(!!TwoDigit::checkTime($time));
                else if ($time->greaterThanOrEqualTo($eveningStart) && $time->lessThanOrEqualTo($eveningLast)) {
                    $this->assertTrue(!!TwoDigit::checkTime($time));
                } else if ($time->greaterThanOrEqualTo($morningStart)) {
                    $this->assertTrue(!!TwoDigit::checkTime($time));
                } else $this->assertFalse(!!TwoDigit::checkTime($time));
            } else {
                $this->assertFalse(!!TwoDigit::checkTime($time));
            }
        }
        file_put_contents(storage_path('logs/debug.log'), '');
    }

    public function test_2d_day()
    {
        for ($i = 0; $i < 60 * 60 * 24 * 365; $i++) {
            $time = today()->startOfYear()->addSeconds($i);
            $today = (clone $time)->startOfDay();
            // Log::channel('debug')->info($time->format("Y-m-d h:i:s A"));
            if ($time->isDayOfWeek(Carbon::SATURDAY))
                $this->assertFalse(TwoDigit::checkDay($time));
            else if (in_array($today, array_map(fn ($value) => new Carbon($value), TwoDigit::CLOSED_DAYS))) {
                if ($today->isDayOfWeek(Carbon::FRIDAY) || $today->isDayOfWeek(Carbon::SATURDAY))
                    $this->assertFalse(TwoDigit::checkDay($time));
                else {
                    if (
                        !in_array((clone $today)->addDay(), array_map(fn ($value) => new Carbon($value), TwoDigit::CLOSED_DAYS))
                        && $time->diffInSeconds($today) >= TwoDigit::EVENING_DURATION + 3600 - 59
                    ) $this->assertTrue(TwoDigit::checkDay($time));
                    else $this->assertFalse(TwoDigit::checkDay($time));
                }
            } else if (in_array($today, array_map(fn ($value) => (new Carbon($value))->subDay(), TwoDigit::CLOSED_DAYS))) {
                if ($today->isDayOfWeek(Carbon::SUNDAY) || $today->isDayOfWeek(Carbon::SATURDAY))
                    $this->assertFalse(TwoDigit::checkDay($time));
                else {
                    if ($time->diffInSeconds($today) <= TwoDigit::EVENING_DURATION)
                        $this->assertTrue(TwoDigit::checkDay($time));
                    else $this->assertFalse(TwoDigit::checkDay($time));
                }
            } else if ($time->isDayOfWeek(Carbon::FRIDAY)) {
                if ($time->diffInSeconds($today) <= TwoDigit::EVENING_DURATION)
                    $this->assertTrue(TwoDigit::checkDay($time));
                else $this->assertFalse(TwoDigit::checkDay($time));
            } else if ($time->isDayOfWeek(Carbon::SUNDAY)) {
                if ($time->diffInSeconds($today) >= TwoDigit::EVENING_DURATION + 3600 - 59) {
                    $this->assertTrue(TwoDigit::checkDay($time));
                } else $this->assertFalse(TwoDigit::checkDay($time));
            } else $this->assertTrue(TwoDigit::checkDay($time));
        }
    }

    public function test_foo()
    {
        $this->assertTrue(today()->isDayOfWeek(Carbon::SUNDAY));
    }
}
