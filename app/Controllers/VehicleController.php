<?php

namespace App\Controllers;

use App\Models\DriverModel;
use App\Models\VehicleModel;

class VehicleController extends BaseController
{
    private const VEHICLE_TYPES = ['bus', 'cab', 'traveller', 'truck', 'tempo', 'private taxi'];

    public function storeForDriver(int $driverId)
    {
        if ($redirect = $this->authorize($this->adminLikeRoles())) {
            return $redirect;
        }

        $driver = (new DriverModel())->find($driverId);
        if (!$driver) {
            return redirect()->to('/drivers')->with('error', 'Driver not found.');
        }

        $rules = $this->getVehicleValidationRules();
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $duplicateWarning = $this->getVehicleDuplicateWarning();
        if ($duplicateWarning !== null) {
            return redirect()->back()->withInput()->with('duplicate_warnings', [$duplicateWarning]);
        }

        $db = \Config\Database::connect();
        $vehicleModel = new VehicleModel();

        $db->transStart();
        $this->retirePrimaryVehicle($vehicleModel, $driverId);
        $vehicleModel->insert($this->buildVehiclePayload($driverId, true));
        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Failed to save vehicle.');
        }

        return redirect()->to('/drivers/' . $driverId . '/edit')->with('success', 'Vehicle assigned successfully.');
    }

    public function quickStore()
    {
        if ($redirect = $this->authorize($this->adminLikeRoles())) {
            return $redirect;
        }

        $driverRules = [
            'driver_name' => 'required|min_length[3]|max_length[150]',
            'mobile_number' => 'required|numeric|min_length[10]|max_length[20]|is_unique[drivers.mobile_number]',
        ];

        $vehicleRules = $this->getVehicleValidationRules();
        if (!$this->validate(array_merge($driverRules, $vehicleRules))) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        if ($this->getVehicleDuplicateWarning() !== null) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['vehicle_number' => 'Another driver already uses this vehicle number.'],
            ]);
        }

        $driverModel = new DriverModel();
        $vehicleModel = new VehicleModel();
        $db = \Config\Database::connect();

        $db->transStart();
        $driverModel->insert([
            'full_name' => trim((string) $this->request->getPost('driver_name')),
            'mobile_number' => trim((string) $this->request->getPost('mobile_number')),
            'whatsapp_number' => $this->emptyToNull($this->request->getPost('mobile_number')),
            'notes' => $this->buildQuickNotes(),
            'status' => 'active',
            'registered_by' => session()->get('user')['id'] ?? null,
            'verified_at' => date('Y-m-d H:i:s'),
        ]);

        $driverId = (int) $driverModel->getInsertID();
        $vehicleModel->insert($this->buildVehiclePayload($driverId, true));
        $db->transComplete();

        if (!$db->transStatus()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['general' => 'Failed to save driver and vehicle.'],
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'msg' => 'Driver and vehicle saved.',
        ]);
    }

    private function getVehicleValidationRules(?int $vehicleId = null): array
    {
        $vehicleUniqueSuffix = $vehicleId ? ',id,' . $vehicleId : '';

        return [
            'vehicle_number' => 'required|max_length[30]|is_unique[vehicles.vehicle_number' . $vehicleUniqueSuffix . ']',
            'vehicle_type' => 'required|in_list[' . implode(',', self::VEHICLE_TYPES) . ']',
        ];
    }

    private function buildVehiclePayload(int $driverId, bool $isPrimary): array
    {
        return [
            'driver_id' => $driverId,
            'vehicle_number' => strtoupper(trim((string) $this->request->getPost('vehicle_number'))),
            'vehicle_type' => strtolower(trim((string) $this->request->getPost('vehicle_type'))),
            'vehicle_brand' => null,
            'vehicle_model' => null,
            'vehicle_color' => null,
            'rc_number' => null,
            'rc_expiry_date' => null,
            'permit_number' => null,
            'permit_expiry_date' => null,
            'insurance_policy_number' => null,
            'insurance_expiry_date' => null,
            'is_primary' => $isPrimary ? 1 : 0,
            'status' => 'active',
            'assigned_from' => date('Y-m-d H:i:s'),
            'assigned_until' => null,
        ];
    }

    private function retirePrimaryVehicle(VehicleModel $vehicleModel, int $driverId): void
    {
        $currentPrimary = $vehicleModel->getPrimaryVehicleForDriver($driverId);
        if (!$currentPrimary) {
            return;
        }

        $vehicleModel->update((int) $currentPrimary['id'], [
            'is_primary' => 0,
            'status' => 'inactive',
            'assigned_until' => date('Y-m-d H:i:s'),
        ]);
    }

    private function getVehicleDuplicateWarning(): ?string
    {
        $vehicleNumber = strtoupper(trim((string) $this->request->getPost('vehicle_number')));
        if ($vehicleNumber === '') {
            return null;
        }

        $existing = (new VehicleModel())
            ->where('vehicle_number', $vehicleNumber)
            ->first();

        return $existing ? 'Another driver already uses this vehicle number.' : null;
    }

    private function buildQuickNotes(): ?string
    {
        $incentive = trim((string) $this->request->getPost('incentive_offered'));
        return $incentive === '' ? null : 'Incentive offered: ' . $incentive;
    }

    private function emptyToNull($value): ?string
    {
        $trimmed = trim((string) $value);
        return $trimmed === '' ? null : $trimmed;
    }
}
