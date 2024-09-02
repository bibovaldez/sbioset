<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogoutController extends Controller
{
    public function logoutOtherSessions($token)
    {
        // Retrieve the logout token from the database
        $logoutToken = DB::table('logout_tokens')->where('token', $token)->first();

        // If token is invalid, abort with a 404 error
        if (!$logoutToken) {
            abort(404);
        }
        // dd($logoutToken->user_id);
        // Delete all sessions associated with the user
        DB::table('sessions')->where('user_id', $logoutToken->user_id)->delete();

        // Remove the token from the database
        DB::table('logout_tokens')->where('token', $token)->delete();

        // Redirect to the homepage with a status message
        return redirect('/')->with('status', 'You have been logged out of other sessions.');
    }
}
