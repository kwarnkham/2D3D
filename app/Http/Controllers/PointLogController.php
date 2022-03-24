<?php

namespace App\Http\Controllers;

use App\Models\PointLog;
use Illuminate\Http\Request;

class PointLogController extends Controller
{
    public function index(Request $request)
    {
        $query = PointLog::query();
        if (!$request->user()->isAdmin()) $query->of($request->user());
        return response()->json(PointLog::paginate($request->per_page ?? 10));
    }
}
