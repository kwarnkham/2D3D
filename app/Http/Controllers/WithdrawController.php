<?php

namespace App\Http\Controllers;

use App\Enums\ResponseStatus;
use App\Models\Payment;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use stdClass;

class WithdrawController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        abort_if(
            $user->hasRecentPasswordChange(),
            ResponseStatus::BAD_REQUEST->value,
            __("messages.To withdraw, you have to wait for 24 hours after changing password")
        );
        $data = $request->validate([
            'amount' => ['required', 'lte:' . $user->mmk()->pivot->balance, 'gte:500', 'integer', 'numeric'],
            'account' => ['required'],
            'payment_id' => ['exists:payments,id'],
            'username' => [Rule::requiredIf($request->payment_id == 1)],
            'password' => ['required']
        ]);
        abort_if(!Hash::check($data['password'], $user->password), ResponseStatus::UNAUTHORIZED->value, __("messages.Incorrect password"));
        $data['point_id'] = 2;
        $user->withdraws()->create($data);
        return response()->json($user->load(User::RS));
    }

    public function index(Request $request)
    {
        $request->validate([
            'status' => ['in:1,2,3,4'],
            'order_in' => ['in:desc,asc']
        ]);
        $query = Withdraw::with(Withdraw::RS)->filter($request->only(['status', 'order_in']));
        if (!$request->user()->isAdmin()) $query->of($request->user());
        return response()->json(
            $query->paginate($request->per_page ?? 10)
        );
    }

    public function list()
    {
        $list = Withdraw::with(Withdraw::RS)->where('status', 2)->whereDay('updated_at', '=', today())->orderBy('id', 'desc')->limit(20)->get();
        $randomAmount = [10000, 15000, 20000, 25000, 23000, 30000, 50000];
        $randomName = [
            "Mortimer Gulgowski",
            "Dr. Hilda Goyette DVM",
            "Prof. Kyler Jacobson PhD",
            "Roger Boyle",
            "Deondre Kuhlman",
            "Naomie Mayert",
            "Vena Ortiz I",
            "Cornelius Ward",
            "Dr. Eden Rosenbaum Jr.",
            "Krystel Farrell",
            "Pinkie Wilkinson",
            "Harmony Funk",
            "Dr. Cooper Davis",
            "Jared Larkin MD",
            "Karolann Stamm III",
            "Franz Koepp III",
            "Mr. Keven Little PhD",
            "Myah Emard",
            "Dr. Nathen Von",
            "Otilia Boehm",
            "Maybelle Wisozk DVM",
            "Elton Rosenbaum II",
            "Mrs. Gerda Fay",
            "Shea Boehm",
            "Mrs. Marcelle Veum",
            "Mrs. Caroline Nicolas",
            "Chelsey Stehr",
            "Carlos Kemmer",
            "Prof. Earnest Predovic",
            "Dr. Efren Oberbrunner",
            "Adrianna Trantow",
            "Elton Klein Jr.",
            "Mr. Harry Lakin MD",
            "Alexanne Strosin",
            "Janelle Champlin",
            "Dr. Kassandra Kassulke II",
            "Jacinto Olson",
            "Helena Fay",
            "Mr. Sigmund Kozey",
            "Lewis Herzog III",
            "Alfonzo Gorczany",
            "Prof. Mckenzie O'Reilly Jr.",
            "Rudolph Lueilwitz II",
            "Alexane Hermann PhD",
            "Ophelia Gleason",
            "Edward Bradtke",
            "Immanuel Monahan II",
            "Roxane Wisoky",
            "Mrs. Myriam Nitzsche",
            "Diego Kohler",
        ];
        $randomMinutes = [48, 66, 88, 11, 35, 14, 28];
        while ($list->count() < 20) {
            $obj = new stdClass();
            $obj->id = $list->last()->id + 1;
            $obj->username = $randomName[array_rand($randomName)];
            $obj->payment = Payment::find([1, 2][array_rand([1, 2])]);
            $obj->amount = $randomAmount[array_rand($randomAmount)];
            $time = now()->subMinutes($randomMinutes[array_rand($randomMinutes)]);
            $obj->updated_at = $time->diffForHumans();
            $obj->updated_time = $time->timestamp;
            $list->push($obj);
        }


        return response()->json(Cache::rememberForever(
            'withdrawList',
            fn () => $list->sortBy('updated_time', descending: true)->values()->all()
        ));
    }

    public function find(Request $request, Withdraw $withdraw)
    {
        $user = $request->user();
        if ($withdraw->user->id != $user->id && !$user->isAdmin()) abort(ResponseStatus::NOT_FOUND->value);
        return response()->json($withdraw->load(Withdraw::RS));
    }

    public function approve(Request $request, Withdraw $withdraw)
    {
        $data = $request->validate([
            'pictures' => ['required', 'array'],
            'pictures.*' => ['image']
        ]);
        Gate::authorize('admin');
        if (!in_array($withdraw->status, ['1', '4'])) abort(ResponseStatus::BAD_REQUEST->value, "Can only approve a pending or drafted Withdraw");
        return response()->json(DB::transaction(function () use ($data, &$withdraw) {
            $withdraw->status = 2;
            $withdraw->save();
            $withdraw->savePictures($data['pictures']);
            return $withdraw->load(Withdraw::RS);
        }));
    }

    public function draft(Request $request, Withdraw $withdraw)
    {
        Gate::authorize('admin');
        if (!in_array($withdraw->status, ['1'])) abort(ResponseStatus::BAD_REQUEST->value, "Can only draft a pending Withdraw");
        $withdraw->status = 4;
        $withdraw->save();
        return response()->json($withdraw->load(Withdraw::RS));
    }

    public function deny(Request $request, Withdraw $withdraw)
    {
        $request->validate([
            'denied_reason' => ['required']
        ]);
        Gate::authorize('admin');
        if (!in_array($withdraw->status, ['1', '4'])) abort(ResponseStatus::BAD_REQUEST->value, "Can only deny a pending or drafted Withdraw");
        $withdraw->status = 3;
        $withdraw->denied_reason = $request->denied_reason;
        $withdraw->save();
        return response()->json($withdraw->load(Withdraw::RS));
    }

    // public function cancel(Request $request, Withdraw $withdraw)
    // {
    //     Gate::authorize('cancel-withdraw', $withdraw);
    //     if (!in_array($withdraw->status, ['1', '4'])) abort(ResponseStatus::BAD_REQUEST->value, "Can only cancel a pending or drafted Withdraw");
    //     $withdraw->status = 5;
    //     $withdraw->save();
    //     return response()->json($withdraw->load(Withdraw::RS));
    // }
}
