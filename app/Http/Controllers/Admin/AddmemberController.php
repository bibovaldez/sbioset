<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;


class AddmemberController extends Controller
{
    protected $user;
    protected $selectedTeamId;
    public function __construct()
    {
        $this->user = Auth::user();
    }
    public function addMember(Request $request)
    {
        $team = Team::where('id', $this->user->current_team_id)->first()->name;
        return view('Admin.admin-add', compact('team'));
    }
}
