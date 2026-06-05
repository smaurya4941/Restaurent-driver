<?php

namespace App\Controllers;

use App\Models\VehicleBranchActivityModel;

class VehicleBranchController extends BaseController
{
    public function index()
    {
        if ($redirect = $this->authorize($this->reportingRoles())) {
            return $redirect;
        }

        $activities = (new VehicleBranchActivityModel())
            ->select('vehicle_branch_activity.*, branches.name AS branch_name, drivers.full_name AS driver_name, vehicles.vehicle_number, vehicles.vehicle_type, users.name AS created_by_name')
            ->join('branches', 'branches.id = vehicle_branch_activity.branch_id', 'left')
            ->join('drivers', 'drivers.id = vehicle_branch_activity.driver_id')
            ->join('vehicles', 'vehicles.id = vehicle_branch_activity.vehicle_id')
            ->join('users', 'users.id = vehicle_branch_activity.created_by_user_id', 'left')
            ->orderBy('vehicle_branch_activity.activity_at', 'DESC')
            ->findAll(200);

        return view('vehicles/branch_activity', ['activities' => $activities]);
    }
}
