<?php

namespace App\Http\Controllers;

use App\Models\PointLog;
use Illuminate\Http\Request;

class PointLogController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'point_id' => ['exists:points,id']
        ]);
        $query = PointLog::with(PointLog::RS)->filter($request->only(['point_id']));
        if (!$request->user()->isAdmin()) $query->of($request->user());
        return response()->json($query->paginate($request->per_page ?? 10));
    }
}
