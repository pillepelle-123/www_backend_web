<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return Inertia::render('settings/profile');
    }

    public function update(Request $request)
    {
        // Profile update logic
        return redirect()->route('profile.edit');
    }

    public function destroy(Request $request)
    {
        // Profile deletion logic
        return redirect('/');
    }
}