<?php

namespace App\Http\Controllers;

use App\Models\AppVersion;
use Illuminate\Http\Request;

class AppVersionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(AppVersion::orderBy('id', 'desc')->first());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AppVersion  $appVersion
     * @return \Illuminate\Http\Response
     */
    public function show(AppVersion $appVersion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AppVersion  $appVersion
     * @return \Illuminate\Http\Response
     */
    public function edit(AppVersion $appVersion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AppVersion  $appVersion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AppVersion $appVersion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AppVersion  $appVersion
     * @return \Illuminate\Http\Response
     */
    public function destroy(AppVersion $appVersion)
    {
        //
    }
}
