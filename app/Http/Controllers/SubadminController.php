<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubadminController extends Controller
{
    public function subadminDashboard()
    {
        return view('subadmin.dashboard');
    }
}
