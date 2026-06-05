<?php

namespace App\Controllers;

use App\Services\DriverLoyaltyService;

class LoyaltyController extends BaseController
{
    public function index()
    {
        if ($redirect = $this->authorize($this->reportingRoles())) {
            return $redirect;
        }

        $service = new DriverLoyaltyService();

        return view('loyalty/index', [
            'leaderboard' => $service->getNationalLeaderboard(100),
        ]);
    }

    public function recompute()
    {
        if ($redirect = $this->authorizeSuperAdmin()) {
            return $redirect;
        }

        $count = (new DriverLoyaltyService())->recomputeAll();

        return redirect()->to('/loyalty')->with('success', 'Recomputed loyalty for ' . $count . ' driver(s).');
    }
}
