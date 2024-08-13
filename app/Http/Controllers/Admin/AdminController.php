<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User; 
use App\Models\Team;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Content\ImageInfoController;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    protected $ImageInfoController;
    public function __construct(ImageInfoController $ImageInfoController)
    {
        $this->ImageInfoController = $ImageInfoController;
    }
    // Example data retrieval
    public function dashboard(Request $request)
    {
        // check admin on what team selected
        $user = Auth::user();
        // Get the selected team 
        $selectedTeamId = $request->input('team_id', $user->current_team_id);

        return view('Admin.admin-dashboard');
    }
    public function calendar(Request $request)
    {
        // check admin on what team selected
        $user = Auth::user();
        // Get the selected team (you might want to store this in the session or as a user preference)
        $selectedTeamId = $request->input('team_id', $user->current_team_id);

        return view('Admin.admin-calendar');
    }
    public function upload()
    {
        return view('Admin.admin-upload');
    }
    // add new member
    public function addMember()
    {
        return view('Admin.admin-add');
    }
}
