<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Assuming you have a User model
use App\Models\Chicken; // Assuming you have a Chicken model

class AdminController extends Controller
{

    // Example data retrieval
    public function index(Request $request)
    {
        // Example data retrieval
        $registeredUsers = $this->getRegisteredUsersCount();
        $totalChicken = $this->getTotalChickenCount();
        $totalHealthyChickens = $this->getTotalHealthyChickensCount();
        $totalUnhealthyChickens = $this->getTotalUnhealthyChickensCount();

        // Prepare data for the view
        $stats = [
            ['title' => 'Registered Users', 'value' => $registeredUsers],
            ['title' => 'Total Chicken', 'value' => $totalChicken],
            ['title' => 'Total Healthy Chickens', 'value' => $totalHealthyChickens],
            ['title' => 'Total Unhealthy Chicken', 'value' => $totalUnhealthyChickens],
        ];

        // Pass data to the view
        return view('admin.index', compact('stats'));
    }

    protected function getRegisteredUsersCount()
    {
        return User::count()-1; // -1 to exclude the admin user
    }

    protected function getTotalChickenCount()
    {
        return Chicken::count();
    }

    protected function getTotalHealthyChickensCount()
    {
        return Chicken::where('status', 'healthy')->count();
    }

    protected function getTotalUnhealthyChickensCount()
    {
        return Chicken::where('status', 'unhealthy')->count();
    }
}
