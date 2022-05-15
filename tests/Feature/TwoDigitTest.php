<?php

namespace Tests\Feature;

use App\Models\JackPot;
use App\Models\JackPotNumber;
use App\Models\JackPotReward;
use App\Models\Point;
use App\Models\PointLog;
use App\Models\TopUp;
use App\Models\TwoDigit;
use App\Models\TwoDigitHit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
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
        \App\Models\JackPotNumber::create(['number' => 0]);
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
        assertTrue(JackPot::getJackPot(false) == 25);
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

        assertTrue(JackPotReward::find(1)->shared_amount == floor(JackPot::where('status', 2)->pluck('amount')->sum() / 2));
        assertTrue(PointLog::where('user_id', $user->id)->orderBy('id', 'desc')->first()->amount == JackPotReward::find(1)->shared_amount);
        assertTrue(PointLog::where('user_id', $user2->id)->orderBy('id', 'desc')->first()->amount == JackPotReward::find(1)->shared_amount);
        assertTrue(JackPotNumber::whereNotNull('hit_at')->first()->number == 0);
        assertTrue(JackPot::where('status', 2)->count() == 2);
        assertTrue(JackPot::where('status', 2)->first()->jack_pot_reward_id == 1);

        assertTrue(TwoDigit::where('number', 0)->orderBy('id', 'desc')->first()->jack_pot_reward_id == 1);
        assertTrue(TwoDigit::where('number', 0)->orderBy('id', 'desc')->first()->two_digit_hit_id == 2);
        assertTrue(PointLog::where('note', 'jackpot prize')->count() == 2);
        assertTrue(PointLog::where('note', '2d prize')->count() == 2);
        assertTrue(JackPotNumber::orderBy('id', 'desc')->first()->number == 1);
    }
}
