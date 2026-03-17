<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->check() && !auth()->user()->onboarding_completed) {
            return redirect()->route('onboarding.show');
        }

        return view('dashboard', [
            'title' => 'dashboard',
        ]);
    }
}
