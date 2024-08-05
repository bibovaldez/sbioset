<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubadminController extends Controller
{
    public function subadminDashboard()
    {
        return view('sub-admin.subadmin-dashboard');
    }
    public function subadminCalendar()
    {
        return view('sub-admin.subadmin-calendar');
    }
}
