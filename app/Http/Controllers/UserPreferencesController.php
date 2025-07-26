<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserPreferencesRequest;
use App\Http\Requests\UpdateUserPreferencesRequest;
use App\Models\UserPreferences;

class UserPreferencesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserPreferencesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(UserPreferences $userPreferences)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserPreferences $userPreferences)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserPreferencesRequest $request, UserPreferences $userPreferences)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserPreferences $userPreferences)
    {
        //
    }
}
