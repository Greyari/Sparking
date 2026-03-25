<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index(){
        return view('landingPage.landing_page', [
            'title' => 'landingPage',
        ]);
    }
}
