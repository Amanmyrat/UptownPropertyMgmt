<?php

namespace App\Http\Controllers;

use App\Models\ChargedBreakdown;
use App\Http\Requests\StoreChargedBreakdownRequest;
use App\Http\Requests\UpdateChargedBreakdownRequest;

class ChargedBreakdownController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreChargedBreakdownRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreChargedBreakdownRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ChargedBreakdown  $chargedBreakdown
     * @return \Illuminate\Http\Response
     */
    public function show(ChargedBreakdown $chargedBreakdown)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ChargedBreakdown  $chargedBreakdown
     * @return \Illuminate\Http\Response
     */
    public function edit(ChargedBreakdown $chargedBreakdown)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateChargedBreakdownRequest  $request
     * @param  \App\Models\ChargedBreakdown  $chargedBreakdown
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateChargedBreakdownRequest $request, ChargedBreakdown $chargedBreakdown)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ChargedBreakdown  $chargedBreakdown
     * @return \Illuminate\Http\Response
     */
    public function destroy(ChargedBreakdown $chargedBreakdown)
    {
        //
    }
}
