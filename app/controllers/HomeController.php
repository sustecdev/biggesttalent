<?php


namespace App\Controllers;

use App\Core\Controller;
use App\Services\LegacyService;
use App\Models\Judge;
use App\Models\Season;

class HomeController extends Controller
{
    protected LegacyService $legacyService;

    public function __construct()
    {
        $this->legacyService = new LegacyService();
    }

    public function index()
    {
        // 1. Handle Legacy Auto-Login (SafeZone Callback)
        $this->legacyService->handleLegacyAutoLogin();

        // 2. Handle Referral Tracking
        $this->legacyService->handleReferralTracking();

        // 3. Get User Balance
        $balanceData = null;
        if (function_exists('isAuthenticated') && isAuthenticated()) {
            if (function_exists('yemchain_get_user_balance')) {
                $balanceData = yemchain_get_user_balance('DBV');
            }
        }

        // 3a. Get Judges Data
        // 3a. Get Judges Data
        $judgeModel = $this->model('Judge');
        $judges = $judgeModel->getAll();

        // 3b. Get Active Season (ensure it's available to all home partials)
        $seasonModel = $this->model('Season');
        $activeSeason = $seasonModel->getActiveSeason();
        $isSeasonActive = (bool) $activeSeason;

        // 4. Prepare Data
        $data = [
            'balanceData' => $balanceData,
            'judges' => $judges,
            'isSeasonActive' => $isSeasonActive,
            'activeSeason' => $activeSeason
        ];

        // 5. Render View
        $this->view('layouts/header', $data);
        $this->view('home/index', $data);
        $this->view('layouts/footer');
    }
}
