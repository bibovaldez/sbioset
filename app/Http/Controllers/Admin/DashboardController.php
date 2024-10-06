<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }
    public function dashboardPoultry(Request $request)
    {
        $dashboardData = $this->dashboardService->getDashboardData();
        $recentUploads = $this->dashboardService->getRecentUploads();
        return view('Admin.admin-dashboard-poultry', compact('dashboardData', 'recentUploads'));
    }

    public function dashboardPiggery(Request $request)
    {
        $dashboardData = $this->dashboardService->getDashboardData();

        return view('Admin.admin-dashboard-piggery', compact('dashboardData'));
    }

    public function dashboardFeeds(Request $request)
    {
        $dashboardData = $this->dashboardService->getDashboardData();

        return view('Admin.admin-dashboard-feeds', compact('dashboardData'));
    }
    // dashboardMedicine
    public function dashboardMedicine(Request $request)
    {
        $dashboardData = $this->dashboardService->getDashboardData();

        return view('Admin.admin-dashboard-medicine', compact('dashboardData'));
    }
}
