<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\AppVersion;
use App\Models\Jackpot;
use App\Models\JackpotNumber;
use App\Models\JackpotReward;
use App\Models\Payment;
use App\Models\Point;
use App\Models\PointLog;
use App\Models\Role;
use App\Models\TopUp;
use App\Models\TwoDigit;
use App\Models\TwoDigitHit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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

    private $user = null;
    private $user2 = null;
    private $admin = null;
    private $config = null;

    protected function setUp(): void
    {
        parent::setUp();
        Point::create(['name' => 'Lucky Hi']);
        Point::create(['name' => 'MMK']);
        AppSetting::create([
            'pool_amount' => '1000000', 'config' => [
                'jackpot_rate' => '0.131',
                'referral_rate' => '0.0532',
                'rate' => '50'
            ]
        ]);
        Payment::create([
            'name' => 'KBZPay', 'mm_name' => 'ကေပေး', 'type' => 1, 'number' => null, 'account_name' => 'SAI KWARN KHAM', 'qr' => 'https://lunarblessing.sgp1.cdn.digitaloceanspaces.com/QR/KpayQR.PNG'
        ]);
        Payment::create([
            'name' => 'WAVEPAY (Wave Money)', 'mm_name' => 'ဝေ့ပေး ဝေ့မန်းနီး', 'type' => 2, 'number' => '09792761207',
        ]);
        Payment::create([
            'name' => 'WAVEPAY (Wave Money) 2', 'mm_name' => 'ဝေ့ပေး ဝေ့မန်းနီး', 'type' => 2, 'number' => '09452538242',
        ]);
        Role::create(['name' => 'admin']);
        JackpotNumber::create(['number' => 0]);
        AppVersion::create(['version' => '1.0.0']);
        $this->user = User::create(['name' => 'moon1', 'password' => '123123']);
        Artisan::call('make:admin moon ninjamoon');
        $this->admin = User::where('name', 'moon')->first();
        $this->user2 = User::create(['name' => 'moon2', 'password' => '123123']);
        $this->config = AppSetting::current()->config;
    }

    public function test_app_logic()
    {
        $this->withoutExceptionHandling();
        $rate = $this->config->rate;
        //top up payment 1
        $response = $this->actingAs($this->user)->postJson('api/top-up', [
            'amount' => 10000,
            'payment_id' => 1,
            'pictures' => [UploadedFile::fake()->image('avatar.jpg')]
        ]);
        $response->assertCreated();
        $response = $this->actingAs($this->admin)->postJson('api/top-up/approve/1', [
            'picture' => UploadedFile::fake()->image('avatar.jpg')
        ]);
        $response->assertOk();
        //top up payment 2
        $response = $this->actingAs($this->user)->postJson('api/top-up', [
            'amount' => 10000,
            'payment_id' => 2,
            'pictures' => [UploadedFile::fake()->image('avatar.jpg')]
        ]);
        $response->assertCreated();
        $response = $this->actingAs($this->admin)->postJson('api/top-up/approve/2', [
            'picture' => UploadedFile::fake()->image('avatar.jpg')
        ]);
        $response->assertOk();
        //top up payment 3
        $response = $this->actingAs($this->user)->postJson('api/top-up', [
            'amount' => 10000,
            'payment_id' => 3,
            'pictures' => [UploadedFile::fake()->image('avatar.jpg')]
        ]);
        $response->assertCreated();
        $response = $this->actingAs($this->admin)->postJson('api/top-up/approve/3', [
            'picture' => UploadedFile::fake()->image('avatar.jpg')
        ]);
        $response->assertOk();
        //bet 2d
        $response = $this->actingAs($this->user)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 0, 'amount' => 100],
                ['number' => 1, 'amount' => 150]
            ],
            'point_id' => 2
        ]);
        $response->assertStatus(201);
        //settle 99

        $response = $this->actingAs($this->admin)->postJson('api/two-digit-hit', [
            'number' => '99',
            'rate' => $rate,
            'set' => '1',
            'value' => '1',
            'day' => now()->greaterThan(today()->addHours(9)->addMinutes(31)) ? today()->addDay()->format("Y/m/d") : today()->format("Y/m/d"),
            'morning' => now()->lessThan(today()->addHours(5)->addMinute()) || now()->greaterThan(today()->addHours(9)->addMinutes(30)->addSeconds(59))
        ]);
        $response->assertStatus(201);
        $this->assertEquals(Jackpot::getJackpot(false), 250 * $this->config->jackpot_rate);
        //update settle
        assertTrue(TwoDigitHit::find(1)->update(['day' => today()->subDay()->format("Y/m/d")]) == 1);

        //top up and approve user 2
        $response = $this->actingAs($this->user2)->postJson('api/top-up', [
            'amount' => 10000,
            'payment_id' => 1,
            'pictures' => [UploadedFile::fake()->image('avatar.jpg')]
        ]);
        $response->assertCreated();
        $response = $this->actingAs($this->admin)->postJson('api/top-up/approve/4', [
            'picture' => UploadedFile::fake()->image('avatar.jpg')
        ]);
        $response->assertOk();

        $response = $this->actingAs($this->user)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 0, 'amount' => 100],
                ['number' => 1, 'amount' => 100]
            ],
            'point_id' => 2
        ]);
        $response->assertStatus(201);

        $response = $this->actingAs($this->user2)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 0, 'amount' => 100],
                ['number' => 1, 'amount' => 100]
            ],
            'point_id' => 2
        ]);
        $response->assertStatus(201);

        $response = $this->actingAs($this->admin)->postJson('api/two-digit-hit', [
            'number' => '0',
            'rate' => $rate,
            'set' => '1',
            'value' => '1',
            'day' => now()->greaterThan(today()->addHours(9)->addMinutes(31)) ? today()->addDay()->format("Y/m/d") : today()->format("Y/m/d"),
            'morning' => now()->lessThan(today()->addHours(5)->addMinute()) || now()->greaterThan(today()->addHours(9)->addMinutes(30)->addSeconds(59))
        ]);
        $response->assertStatus(201);

        assertTrue(JackpotReward::find(1)->shared_amount == floor(Jackpot::where('status', 2)->pluck('amount')->sum() / 2));
        assertTrue(PointLog::where('user_id', $this->user->id)->orderBy('id', 'desc')->first()->amount == JackpotReward::find(1)->shared_amount);
        assertTrue(PointLog::where('user_id', $this->user2->id)->orderBy('id', 'desc')->first()->amount == JackpotReward::find(1)->shared_amount);
        assertTrue(JackpotNumber::whereNotNull('hit_at')->first()->number == 0);
        assertTrue(Jackpot::where('status', 2)->count() == 2);
        assertTrue(Jackpot::where('status', 2)->first()->jackpot_reward_id == 1);
        assertTrue(TwoDigit::where('number', 0)->orderBy('id', 'desc')->first()->jackpot_reward_id == 1);
        assertTrue(TwoDigit::where('number', 0)->orderBy('id', 'desc')->first()->two_digit_hit_id == 2);
        assertTrue(PointLog::where('note', 'jackpot prize')->count() == 2);
        assertTrue(PointLog::where('note', '2d prize')->count() == 2);
        assertTrue(JackpotNumber::orderBy('id', 'desc')->first()->number == 1);
        $this->assertEquals($this->user->getBalanceByPoint(Point::find(2)), (30000 - 450 + (100 * $rate) + JackpotReward::find(1)->shared_amount));
        $this->assertEquals($this->user2->getBalanceByPoint(Point::find(2)), (10000 - 200 + (100 * $rate) + JackpotReward::find(1)->shared_amount));
    }


    public function test_referral_code()
    {
        $this->withExceptionHandling();
        $user = User::create([
            'referrer_id' => $this->user->id,
            'name' => 'test',
            'password' => 'test'
        ]);

        $amount = 1000;

        $this->actingAs($user)->postJson('api/top-up', [
            'amount' => $amount,
            'payment_id' => 1,
            'pictures' => [UploadedFile::fake()->image('avatar.jpg')]
        ]);

        $this->actingAs($this->admin)->postJson('api/top-up/approve/1', [
            'picture' => UploadedFile::fake()->image('avatar.jpg')
        ]);

        $this->assertEquals($user->getBalanceByPoint(Point::find(2)), $amount);
        $this->assertEquals($user->getReferableBalanceByPoint(Point::find(2)), $amount);

        $response = $this->actingAs($user)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 0, 'amount' => 100],
                ['number' => 1, 'amount' => 100]
            ],
            'point_id' => 2
        ]);

        $response->assertCreated();

        $this->assertDatabaseCount('referral_rewards', 1);
        $this->assertEquals($user->getBalanceByPoint(Point::find(2)), $amount - 200);
        $this->assertEquals($user->getReferableBalanceByPoint(Point::find(2)), $amount - 200);
        $this->assertEquals($this->user->getBalanceByPoint(Point::find(2)), 200 * $this->config->referral_rate);

        $this->actingAs($this->admin)->postJson('api/two-digit-hit', [
            'number' => '0',
            'set' => '1',
            'value' => '1',
            'rate' => $this->config->rate,
            'day' => now()->greaterThan(today()->addHours(9)->addMinutes(31)) ? today()->addDay()->format("Y/m/d") : today()->format("Y/m/d"),
            'morning' => now()->lessThan(today()->addHours(5)->addMinute()) || now()->greaterThan(today()->addHours(9)->addMinutes(30)->addSeconds(59))
        ]);

        $this->assertEquals($user->getBalanceByPoint(Point::find(2)), $amount - 200 + (100 * $this->config->rate));
        assertTrue(TwoDigitHit::find(1)->update(['day' => today()->subDay()->format("Y/m/d")]) == 1);

        $response = $this->actingAs($user)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 1, 'amount' => $amount - 200 + (100 * $this->config->rate)]
            ],
            'point_id' => 2
        ]);

        $response->assertCreated();

        $this->assertEquals($user->getBalanceByPoint(Point::find(2)), 0);
        $this->assertEquals($this->user->getBalanceByPoint(Point::find(2)), $amount * $this->config->referral_rate);
        $this->assertDatabaseCount('referral_rewards', 2);
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

    public function test_get_2d_effected_query()
    {
        $response = $this->actingAs($this->user)->postJson('api/top-up', [
            'amount' => 60 * 60 * 24 * 365 * 100,
            'payment_id' => 1,
            'pictures' => [UploadedFile::fake()->image('avatar.jpg')]
        ]);
        $response->assertCreated();
        $response = $this->actingAs($this->admin)->postJson('api/top-up/approve/1', [
            'picture' => UploadedFile::fake()->image('avatar.jpg')
        ]);
        $response->assertOk();
        for ($i = 442380; $i < 60 * 60 * 24 * 365; $i++) {
            $time = today()->startOfYear()->addSeconds($i);
            if (TwoDigit::checkTime($time)) {
                Log::channel('debug')->info($time->format("Y-m-d D H:i:s A"));
                $result = $this->user->twoDigits()->create([
                    'number' => '00',
                    'amount' => '100',
                    'point_id' => '2',
                    'created_at' => $time
                ]);
                // Log::channel('debug')->info($result);
                $this->assertEquals(1, TwoDigit::getQueryBuilderOfEffectedNumbers($time)->count());
                DB::table('two_digits')->truncate();
                file_put_contents(storage_path('logs/debug.log'), '');
            }
        }
    }
}
