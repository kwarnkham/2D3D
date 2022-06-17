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

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertGreaterThan;
use function PHPUnit\Framework\assertNotEquals;
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
    private $appSetting = null;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('init:database');
        $this->admin = User::where('name', 'moon')->first();
        $this->user2 = User::create(['name' => 'moon2', 'password' => '123123']);
        $this->user = User::create(['name' => 'moon1', 'password' => '123123']);
        $this->appSetting = AppSetting::current();
    }

    public function test_pool_amount_reduced()
    {

        $prev = $this->appSetting->pool_amount;
        $response = $this->actingAs($this->user)->postJson('api/top-up', [
            'amount' => 100000,
            'payment_id' => 1,
            'pictures' => [UploadedFile::fake()->image('avatar.jpg')]
        ]);
        $response->assertCreated();
        $response = $this->actingAs($this->admin)->postJson('api/top-up/approve/1', [
            'picture' => UploadedFile::fake()->image('avatar.jpg')
        ]);
        $response->assertOk();

        $response = $this->actingAs($this->user)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 99, 'amount' => 100]
            ],
            'point_id' => 2
        ]);
        $response->assertCreated();
        $this->assertEquals(100000 - 100, $this->user->getBalanceByPoint(Point::find(2)));

        $response = $this->actingAs($this->admin)->postJson('api/two-digit-hit', [
            'number' => '99',
            'rate' => $this->appSetting->rate,
            'set' => '1',
            'value' => '1',
            'day' => now()->greaterThan(today()->addHours(9)->addMinutes(31)) ? today()->addDay()->format("Y/m/d") : today()->format("Y/m/d"),
            'morning' => now()->lessThan(today()->addHours(5)->addMinute()) || now()->greaterThan(today()->addHours(9)->addMinutes(30)->addSeconds(59))
        ]);
        $response->assertStatus(201);

        $this->assertEquals(100000 - 100 + (100 * $this->appSetting->rate), $this->user->getBalanceByPoint(Point::find(2)));
        $this->assertEquals($prev - (100 * $this->appSetting->rate), $this->appSetting->pool_amount);
    }

    public function test_max_bet()
    {
        $response = $this->actingAs($this->user)->postJson('api/top-up', [
            'amount' => 100000,
            'payment_id' => 1,
            'pictures' => [UploadedFile::fake()->image('avatar.jpg')]
        ]);
        $response->assertCreated();
        $response = $this->actingAs($this->admin)->postJson('api/top-up/approve/1', [
            'picture' => UploadedFile::fake()->image('avatar.jpg')
        ]);
        $response->assertOk();

        $betData = [];
        for ($i = 0; $i < $this->appSetting->max_bet; $i++) {
            $betData[] = ['number' => $i, 'amount' => 100];
        }
        $response = $this->actingAs($this->user)->postJson('api/two-digit', [
            'numbers' => $betData,
            'point_id' => 2
        ]);
        $response->assertStatus(201);

        $response = $this->actingAs($this->user)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 99, 'amount' => 100]
            ],
            'point_id' => 2
        ]);
        $response->assertStatus(400);
    }

    public function test_jackpot_distribution()
    {
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

        $response = $this->actingAs($this->user2)->postJson('api/top-up', [
            'amount' => 10000,
            'payment_id' => 1,
            'pictures' => [UploadedFile::fake()->image('avatar.jpg')]
        ]);
        $response->assertCreated();
        $response = $this->actingAs($this->admin)->postJson('api/top-up/approve/2', [
            'picture' => UploadedFile::fake()->image('avatar.jpg')
        ]);
        $response->assertOk();

        $response = $this->actingAs($this->user)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 0, 'amount' => 100],
            ],
            'point_id' => 2
        ]);
        $response->assertStatus(201);

        $response = $this->actingAs($this->user2)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 0, 'amount' => 200],
            ],
            'point_id' => 2
        ]);
        $response->assertStatus(201);

        $response = $this->actingAs($this->admin)->postJson('api/two-digit-hit', [
            'number' => '99',
            'rate' => $this->appSetting->rate,
            'set' => '1',
            'value' => '1',
            'day' => now()->greaterThan(today()->addHours(9)->addMinutes(31)) ? today()->addDay()->format("Y/m/d") : today()->format("Y/m/d"),
            'morning' => now()->lessThan(today()->addHours(5)->addMinute()) || now()->greaterThan(today()->addHours(9)->addMinutes(30)->addSeconds(59))
        ]);
        $response->assertStatus(201);
        $jackpotAmount = Jackpot::getJackpot(false);
        $this->assertEquals($jackpotAmount, 300 * $this->appSetting->jackpot_rate);
        //update settle
        assertTrue(TwoDigitHit::find(1)->update(['day' => today()->subDay()->format("Y/m/d")]) == 1);

        $response = $this->actingAs($this->user)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 0, 'amount' => 123],
            ],
            'point_id' => 2
        ]);
        $response->assertStatus(201);

        $response = $this->actingAs($this->user2)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 0, 'amount' => 456],
            ],
            'point_id' => 2
        ]);
        $response->assertStatus(201);
        $response = $this->actingAs($this->admin)->postJson('api/two-digit-hit', [
            'number' => '00',
            'rate' => $this->appSetting->rate,
            'set' => '1',
            'value' => '1',
            'day' => now()->greaterThan(today()->addHours(9)->addMinutes(31)) ? today()->addDay()->format("Y/m/d") : today()->format("Y/m/d"),
            'morning' => now()->lessThan(today()->addHours(5)->addMinute()) || now()->greaterThan(today()->addHours(9)->addMinutes(30)->addSeconds(59))
        ]);
        $response->assertStatus(201);

        $userJackpot = JackpotReward::find(1)->users()->where('user_id', $this->user->id)->first()->pivot->reward;
        $user2Jackpot = JackpotReward::find(1)->users()->where('user_id', $this->user2->id)->first()->pivot->reward;

        assertNotEquals($userJackpot, floor(Jackpot::where('status', 2)->pluck('amount')->sum() / 2));

        assertEquals(PointLog::where('user_id', $this->user->id)->orderBy('id', 'desc')->first()->amount, $userJackpot);

        assertEquals(PointLog::where('user_id', $this->user2->id)->orderBy('id', 'desc')->first()->amount, $user2Jackpot);

        assertGreaterThan($userJackpot, $user2Jackpot);

        assertEquals(floor($jackpotAmount * (123 / (123 + 456) * 100) / 100), $userJackpot);

        assertEquals(floor($jackpotAmount * (456 / (123 + 456) * 100) / 100), $user2Jackpot);

        assertTrue(JackpotNumber::whereNotNull('hit_at')->first()->number == 0);
        assertTrue(Jackpot::where('status', 2)->count() == 2);
        assertTrue(Jackpot::where('status', 2)->first()->jackpot_reward_id == 1);
        assertTrue(TwoDigit::where('number', 0)->orderBy('id', 'desc')->first()->jackpot_reward_id == 1);
        assertTrue(TwoDigit::where('number', 0)->orderBy('id', 'desc')->first()->two_digit_hit_id == 2);
        assertTrue(PointLog::where('note', 'jackpot prize')->count() == 2);
        assertTrue(PointLog::where('note', '2d prize')->count() == 2);
        assertTrue(JackpotNumber::orderBy('id', 'desc')->first()->number == 11);

        $this->assertEquals($this->user->getBalanceByPoint(Point::find(2)), (10000 - 100 - 123 + (123 * $this->appSetting->rate) + $userJackpot));
        $this->assertEquals($this->user2->getBalanceByPoint(Point::find(2)), (10000 - 200 - 456 + (456 * $this->appSetting->rate) + $user2Jackpot));
    }

    public function test_app_logic()
    {
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
            'rate' => $this->appSetting->rate,
            'set' => '1',
            'value' => '1',
            'day' => now()->greaterThan(today()->addHours(9)->addMinutes(31)) ? today()->addDay()->format("Y/m/d") : today()->format("Y/m/d"),
            'morning' => now()->lessThan(today()->addHours(5)->addMinute()) || now()->greaterThan(today()->addHours(9)->addMinutes(30)->addSeconds(59))
        ]);
        $response->assertStatus(201);
        $this->assertEquals(Jackpot::getJackpot(false), 250 * $this->appSetting->jackpot_rate);
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
            'rate' => $this->appSetting->rate,
            'set' => '1',
            'value' => '1',
            'day' => now()->greaterThan(today()->addHours(9)->addMinutes(31)) ? today()->addDay()->format("Y/m/d") : today()->format("Y/m/d"),
            'morning' => now()->lessThan(today()->addHours(5)->addMinute()) || now()->greaterThan(today()->addHours(9)->addMinutes(30)->addSeconds(59))
        ]);
        $response->assertStatus(201);

        assertTrue(JackpotReward::find(1)->shared_amount == floor(Jackpot::where('status', 2)->pluck('amount')->sum() / 2));

        assertEquals(PointLog::where('user_id', $this->user->id)->orderBy('id', 'desc')->first()->amount, JackpotReward::find(1)->shared_amount);
        assertTrue(PointLog::where('user_id', $this->user2->id)->orderBy('id', 'desc')->first()->amount == JackpotReward::find(1)->shared_amount);
        assertTrue(JackpotNumber::whereNotNull('hit_at')->first()->number == 0);
        assertTrue(Jackpot::where('status', 2)->count() == 2);
        assertTrue(Jackpot::where('status', 2)->first()->jackpot_reward_id == 1);
        assertTrue(TwoDigit::where('number', 0)->orderBy('id', 'desc')->first()->jackpot_reward_id == 1);
        assertTrue(TwoDigit::where('number', 0)->orderBy('id', 'desc')->first()->two_digit_hit_id == 2);
        assertTrue(PointLog::where('note', 'jackpot prize')->count() == 2);
        assertTrue(PointLog::where('note', '2d prize')->count() == 2);
        assertTrue(JackpotNumber::orderBy('id', 'desc')->first()->number == 11);
        $this->assertEquals($this->user->getBalanceByPoint(Point::find(2)), (30000 - 450 + (100 * $this->appSetting->rate) + JackpotReward::find(1)->shared_amount));
        $this->assertEquals($this->user2->getBalanceByPoint(Point::find(2)), (10000 - 200 + (100 * $this->appSetting->rate) + JackpotReward::find(1)->shared_amount));
    }


    public function test_referral_code()
    {
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
        $this->assertEquals($this->user->getBalanceByPoint(Point::find(2)), 200 * $this->appSetting->referral_rate);

        $this->actingAs($this->admin)->postJson('api/two-digit-hit', [
            'number' => '0',
            'set' => '1',
            'value' => '1',
            'rate' => $this->appSetting->rate,
            'day' => now()->greaterThan(today()->addHours(9)->addMinutes(31)) ? today()->addDay()->format("Y/m/d") : today()->format("Y/m/d"),
            'morning' => now()->lessThan(today()->addHours(5)->addMinute()) || now()->greaterThan(today()->addHours(9)->addMinutes(30)->addSeconds(59))
        ]);

        $this->assertEquals($user->getBalanceByPoint(Point::find(2)), $amount - 200 + (100 * $this->appSetting->rate));
        assertTrue(TwoDigitHit::find(1)->update(['day' => today()->subDay()->format("Y/m/d")]) == 1);

        $response = $this->actingAs($user)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 1, 'amount' => $amount - 200 + (100 * $this->appSetting->rate)]
            ],
            'point_id' => 2
        ]);

        $response->assertCreated();

        $this->assertEquals($user->getBalanceByPoint(Point::find(2)), 0);
        $this->assertEquals($this->user->getBalanceByPoint(Point::find(2)), $amount * $this->appSetting->referral_rate);
        $this->assertDatabaseCount('referral_rewards', 2);
    }

    public function test_2d_time()
    {
        for ($i = 0; $i < 60 * 60 * 24; $i++) {
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
        $start = 60 * 60 * 24 * 30 * 4;
        $end = 60 * 60 * 24 * 30 * 5;
        for ($i = $start; $i < $end; $i++) {
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
        for ($i = 0; $i < 60 * 60 * 24; $i++) {
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

    public function test_get_2d_result()
    {
        return;
        $dates = [
            today(),
            today()->subDays(1),
            today()->subDays(2),
            today()->subDays(3),
            today()->subDays(4),
            today()->subDays(5),
            today()->subDays(6),
            today()->subDays(7),
            today()->subDays(8),
            today()->subDays(9),
        ];
        $results = [
            ['53', null],
            ['07', '11'],
            ['23', '50'],
            ['71', '30'],
            ['12', '85'],
            ['73', '33'],
            ['84', '84'],
        ];
        $dates = array_filter($dates, fn ($value) => !($value->isDayOfWeek(Carbon::SUNDAY) || $value->isDayOfWeek(Carbon::SATURDAY)));
        $dates = array_values(array_filter($dates, fn ($value) => !in_array($value, array_map(fn ($val) => new Carbon($val), TwoDigit::CLOSED_DAYS))));
        foreach ($dates as $key => $value) {
            // dump($value->format("D d-m-Y"));
            // dump($key);
            // dump(TwoDigit::getResult((clone $value)->addHours(5)->addMinutes(40)));
            // dump($results[$key][0]);
            // dump(TwoDigit::getResult((clone $value)->addHours(10)->addMinutes(10)));
            // dump($results[$key][1]);
            $this->assertEquals($results[$key][0], TwoDigit::getResult((clone $value)->addHours(5)->addMinutes(40), false));
            $this->assertEquals($results[$key][1], TwoDigit::getResult((clone $value)->addHours(10)->addMinutes(10), false));
        }
    }

    public function test_settle_day()
    {
        for ($i = 0; $i < 400; $i++) {
            $time = today()->startOfYear()->addDays($i);
            if (in_array($time, array_map(fn ($value) => new Carbon($value), TwoDigit::CLOSED_DAYS))) {
                $this->assertFalse(TwoDigitHit::checkDay($time));
            } else if ($time->isDayOfWeek(Carbon::SATURDAY) || $time->isDayOfWeek(Carbon::SUNDAY)) {
                $this->assertFalse(TwoDigit::checkDay($time));
            } else $this->assertTrue(TwoDigitHit::checkDay($time));
            // dump($time->format('Y-m-d D'));
        }
    }

    public function test_max_prize()
    {
        $users = [];
        for ($i = 0; $i < 11; $i++) {
            $users[] = User::create(['name' => 'user' . $i, 'password' => '123123']);
        }
        $this->assertEquals(11, count($users));

        foreach ($users as $key => $user) {
            $response = $this->actingAs($user)->postJson('api/top-up', [
                'amount' => 10000,
                'payment_id' => 1,
                'pictures' => [UploadedFile::fake()->image('avatar.jpg')]
            ]);
            $response->assertCreated();

            $response = $this->actingAs($this->admin)->postJson('api/top-up/approve/' . $key + 1, [
                'picture' => UploadedFile::fake()->image('avatar.jpg')
            ]);
            $response->assertOk();

            $response = $this->actingAs($user)->postJson('api/two-digit', [
                'numbers' => [
                    ['number' => 0, 'amount' => 1000],
                ],
                'point_id' => 2
            ]);
            $response->assertStatus(201);
        }

        $this->assertEquals($this->appSetting->pool_amount + 11000, TwoDigit::getMaxPrize(99));

        $response = $this->actingAs($this->user)->postJson('api/top-up', [
            'amount' => 100000,
            'payment_id' => 1,
            'pictures' => [UploadedFile::fake()->image('avatar.jpg')]
        ]);
        $response->assertCreated();

        $response = $this->actingAs($this->admin)->postJson('api/top-up/approve/' . count($users) + 1, [
            'picture' => UploadedFile::fake()->image('avatar.jpg')
        ]);
        $response->assertOk();

        $response = $this->actingAs($this->user)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 1, 'amount' => 2000],
                ['number' => 2, 'amount' => 2000],
                ['number' => 3, 'amount' => 2000],
                ['number' => 4, 'amount' => 2000],
                ['number' => 5, 'amount' => 2000],
                ['number' => 6, 'amount' => 2000],
                ['number' => 7, 'amount' => 2000],
                ['number' => 8, 'amount' => 2000],
                ['number' => 9, 'amount' => 2000],
                ['number' => 10, 'amount' => 2000],
            ],
            'point_id' => 2
        ]);

        $response->assertStatus(201);
        $this->assertEquals($this->appSetting->pool_amount + 11000 + 20000, TwoDigit::getMaxPrize(99));

        $response = $this->actingAs($this->user)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 0, 'amount' => 1000],
            ],
            'point_id' => 2
        ]);
        $response->assertStatus(201);



        // $response = $this->actingAs($this->user2)->postJson('api/two-digit', [
        //     'numbers' => [
        //         ['number' => 0, 'amount' => 100],
        //     ],
        //     'point_id' => 2
        // ]);

        // $response->assertStatus(400);

        $this->assertEquals($this->appSetting->pool_amount + 11000 + 20000 + 1000, TwoDigit::getMaxPrize(99));
        $this->assertEquals($this->appSetting->pool_amount + 11000 + 20000 + 1000 - 2000, TwoDigit::getMaxPrize(1));
        $this->assertEquals($this->appSetting->pool_amount + 11000 + 20000 + 1000 - 2000, TwoDigit::getMaxPrize(10));
        $this->assertEquals($this->appSetting->pool_amount + 11000 + 20000 + 1000 - 12000, TwoDigit::getMaxPrize(0));

        $response = $this->actingAs($this->user)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 1, 'amount' => 2000],
                ['number' => 2, 'amount' => 2000],
                ['number' => 3, 'amount' => 2000],
                ['number' => 4, 'amount' => 2000],
                ['number' => 5, 'amount' => 2000],
            ],
            'point_id' => 2
        ]);

        $response->assertStatus(201);

        $response = $this->actingAs($this->user)->postJson('api/two-digit', [
            'numbers' => [
                ['number' => 0, 'amount' => 117.64705],
            ],
            'point_id' => 2
        ]);
        $response->assertStatus(201);

        // $response = $this->actingAs($this->user)->postJson('api/two-digit', [
        //     'numbers' => [
        //         ['number' => 1, 'amount' => 2000],
        //         ['number' => 2, 'amount' => 2000],
        //         ['number' => 2, 'amount' => 2000],
        //         ['number' => 3, 'amount' => 1000],
        //     ],
        //     'point_id' => 2
        // ]);

        // $response->assertStatus(201);

        // $response = $this->actingAs($this->user)->postJson('api/two-digit', [
        //     'numbers' => [
        //         ['number' => 0, 'amount' => 100],
        //     ],
        //     'point_id' => 2
        // ]);
        // $response->assertStatus(201);
    }
}
