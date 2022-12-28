<?php

namespace App\Http\Controllers;

use App\Models\PropertyRentCollection;
use App\Http\Requests\StorePropertyRentCollectionsRequest;
use App\Http\Requests\UpdatePropertyRentCollectionsRequest;

class PropertyRentCollectionController extends Controller
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
     * @param  \App\Http\Requests\StorePropertyRentCollectionsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePropertyRentCollectionsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PropertyRentCollections  $propertyRentCollections
     * @return \Illuminate\Http\Response
     */
    public function show(PropertyRentCollections $propertyRentCollections)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PropertyRentCollections  $propertyRentCollections
     * @return \Illuminate\Http\Response
     */
    public function edit(PropertyRentCollections $propertyRentCollections)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePropertyRentCollectionsRequest  $request
     * @param  \App\Models\PropertyRentCollections  $propertyRentCollections
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePropertyRentCollectionsRequest $request, PropertyRentCollections $propertyRentCollections)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PropertyRentCollections  $propertyRentCollections
     * @return \Illuminate\Http\Response
     */
    public function destroy(PropertyRentCollections $propertyRentCollections)
    {
        //
    }
}
