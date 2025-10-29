<?php

namespace App\Http\Controllers;

class Welcome extends Controller
{
    public function index()
    {
        $title = 'Home';

        return view('welcome', compact('title'));
    }
}
