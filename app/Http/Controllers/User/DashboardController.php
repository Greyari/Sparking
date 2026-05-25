<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->check() && !auth()->user()->onboarding_completed) {
            return redirect()->route('onboarding.show');
        }

        return view('user.dashboard', [
            'title' => 'dashboard',
        ]);
    }
}
