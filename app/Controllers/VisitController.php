<?php

namespace App\Controllers;

use App\Models\DriverModel;
use App\Models\VehicleBranchActivityModel;
use App\Models\VehicleModel;
use App\Models\VisitModel;
use App\Services\DriverLoyaltyService;
use App\Services\GeocodingService;
use App\Services\IncentiveEngineService;

class VisitController extends BaseController
{
    private const VISIT_VERIFICATION_METHODS = ['phone', 'qr', 'manual'];
    private const VEHICLE_TYPES = ['car 5 seat', 'car 7 seat', 'bus', 'traveller'];
    private const DUPLICATE_CHECKIN_HOURS = 1;
    private const MAX_VISITS_PER_DAY = 5;

    public function index()
    {
        if ($redirect = $this->authorize($this->visitEntryRoles())) {
            return $redirect;
        }

        $searchTerm = trim((string) $this->request->getVar('search_term'));
        $driver = null;

        if ($searchTerm !== '') {
            $driver = $this->findDriverForVisitSearch($searchTerm);
        }

        return view('visitEntry', [
            'driver' => $driver,
            'search_term' => $searchTerm,
            'visits' => $this->getVisitListing(),
            'verificationMethods' => self::VISIT_VERIFICATION_METHODS,
            'vehicleTypeOptions' => self::VEHICLE_TYPES,
            'visitDefaults' => [
                'visited_at' => date('Y-m-d\TH:i'),
            ],
        ]);
    }

    public function list()
    {
        if ($redirect = $this->authorize($this->visitEntryRoles())) {
            return $redirect;
        }

        return view('visitEntryList', [
            'visits' => $this->getVisitListing(),
        ]);
    }

    public function store()
    {
        if ($redirect = $this->authorize($this->visitEntryRoles())) {
            return $redirect;
        }

        $branchId = $this->requireBranchId();

        $validationRules = [
            'driver_id' => 'required|is_natural_no_zero',
            // 'verification_method' => 'in_list[' . implode(',', self::VISIT_VERIFICATION_METHODS) . ']',
            'verification_reference' => 'permit_empty|max_length[120]',
            'visited_at' => 'required',
            'vehicle_number' => 'required|max_length[30]',
            'vehicle_type' => 'required|in_list[' . implode(',', self::VEHICLE_TYPES) . ']',
            'guest_count' => 'required|is_natural',
            'food_offered' => 'required|in_list[0,1]',
            'cash_incentive_amount' => 'required|numeric|greater_than_equal_to[0]',
            'remarks' => 'permit_empty|max_length[1000]',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $driverId = (int) $this->request->getPost('driver_id');
        $driver = $this->getDriverDetails($driverId);
        if (!$driver) {
            return redirect()->to('/visitEntry')->with('error', 'Driver not found.');
        }

        if (in_array($driver['status'] ?? '', ['blocked', 'blacklisted'], true)) {
            return redirect()->back()->withInput()->with('error', 'This driver is marked as ' . ($driver['status'] ?? 'restricted') . ' and cannot be checked in.');
        }

        $visitedAtInput = (string) $this->request->getPost('visited_at');
        $visitedAt = date('Y-m-d H:i:s', strtotime($visitedAtInput));
        $duplicateError = $this->validateVisitFrequency($driverId, $visitedAt, $branchId);
        if ($duplicateError !== null) {
            return redirect()->back()->withInput()->with('error', $duplicateError);
        }

        $foodOffered = (int) $this->request->getPost('food_offered') === 1;

        $currentUserId = session()->get('user')['id'] ?? null;
        $vehicleId = $this->resolveVisitVehicle($driverId, $driver);
        if ($vehicleId === null) {
            return redirect()->back()->withInput()->with('error', 'Vehicle number is already assigned to another driver.');
        }

        $cashIncentiveAmount = $this->decimalOrZero($this->request->getPost('cash_incentive_amount'));
        $visitPayload = [
            'branch_id' => $branchId,
            'driver_id' => $driverId,
            'vehicle_id' => $vehicleId,
            'visited_at' => $visitedAt,
            'guest_count' => (int) $this->request->getPost('guest_count'),
            'food_offered' => $foodOffered ? 1 : 0,
            'food_quantity' => null,
            'food_type' => null,
            'cash_incentive_amount' => $cashIncentiveAmount,
            'amount_paid' => '0.00',
            'verified_by_user_id' => $currentUserId,
            'handled_by_user_id' => $currentUserId,
            'incentive_given_by_user_id' => (float) $cashIncentiveAmount > 0 ? $currentUserId : null,
            // 'verification_method' => (string) $this->request->getPost('verification_method'),
            'verification_reference' => $this->emptyToNull($this->request->getPost('verification_reference')),
            'remarks' => $this->emptyToNull($this->request->getPost('remarks')),
        ] + $this->resolveGeolocationPayload();

        $visitModel = new VisitModel();
        $visitModel->insert($visitPayload);
        $visitId = (int) $visitModel->getInsertID();
        $this->recordVehicleActivity($branchId, $vehicleId, $driverId, $visitId, $visitedAt);
        (new IncentiveEngineService())->recomputeForVisitDate($driverId, $visitedAt, $branchId);
        (new DriverLoyaltyService())->recomputeDriver($driverId);
        $this->logAudit('visit.created', 'visit', $visitId, null, $visitPayload);

        return redirect()->to('/visitEntry')->with('success', 'Visit entry saved successfully.');
    }

    public function storeNewDriverVisit()
    {
        if ($redirect = $this->authorize($this->visitEntryRoles())) {
            return $redirect;
        }

        $branchId = $this->requireBranchId();

        $validationRules = array_merge(
            $this->getInlineDriverValidationRules(),
            $this->getInlineVehicleValidationRules(),
            $this->getVisitValidationRules()
        );

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $duplicateWarnings = $this->detectRegistrationDuplicates();
        if ($duplicateWarnings !== []) {
            return redirect()->back()->withInput()->with('error', implode(' ', $duplicateWarnings));
        }

        $visitedAt = date('Y-m-d H:i:s', strtotime((string) $this->request->getPost('visited_at')));
        $foodOffered = (int) $this->request->getPost('food_offered') === 1;

        // Resolve geolocation (may hit an external geocoder) before opening the
        // transaction so a slow lookup never holds table locks open.
        $geolocation = $this->resolveGeolocationPayload();

        $db = \Config\Database::connect();
        $driverModel = new DriverModel();
        $vehicleModel = new VehicleModel();
        $visitModel = new VisitModel();
        $currentUserId = session()->get('user')['id'] ?? null;

        $db->transStart();

        $driverModel->insert($this->buildInlineDriverPayload());
        $driverId = (int) $driverModel->getInsertID();

        $vehicleModel->insert($this->buildInlineVehiclePayload($driverId));
        $vehicleId = (int) $vehicleModel->getInsertID();

        $cashIncentiveAmount = $this->decimalOrZero($this->request->getPost('default_cash_incentive_amount') ?: '200');
        $visitPayload = [
            'branch_id' => $branchId,
            'driver_id' => $driverId,
            'vehicle_id' => $vehicleId,
            'visited_at' => $visitedAt,
            'guest_count' => (int) $this->request->getPost('guest_count'),
            'food_offered' => $foodOffered ? 1 : 0,
            'food_quantity' => null,
            'food_type' => null,
            'cash_incentive_amount' => $cashIncentiveAmount,
            'amount_paid' => '0.00',
            'verified_by_user_id' => $currentUserId,
            'handled_by_user_id' => $currentUserId,
            'incentive_given_by_user_id' => (float) $cashIncentiveAmount > 0 ? $currentUserId : null,
            'verification_method' => (string) $this->request->getPost('verification_method'),
            'verification_reference' => $this->emptyToNull($this->request->getPost('verification_reference')),
            'remarks' => $this->emptyToNull($this->request->getPost('remarks')),
        ] + $geolocation;

        $visitModel->insert($visitPayload);
        $visitId = (int) $visitModel->getInsertID();
        $this->recordVehicleActivity($branchId, $vehicleId, $driverId, $visitId, $visitedAt);

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Failed to register driver and save visit.');
        }

        (new IncentiveEngineService())->recomputeForVisitDate($driverId, $visitedAt, $branchId);
        (new DriverLoyaltyService())->recomputeDriver($driverId);
        $this->logAudit('driver.created', 'driver', $driverId, null, ['source' => 'visitEntryInline']);
        $this->logAudit('visit.created', 'visit', $visitId, null, $visitPayload);

        return redirect()->to('/visitEntry')->with('success', 'New driver registered and first visit logged successfully.');
    }

    public function edit($id)
    {
        if (!in_array((int) session()->get('role'), $this->adminLikeRoles(), true)) {
            return redirect()->to('/dashboard');
        }

        $visit = $this->getVisitDetails((int) $id);
        if (!$visit) {
            return redirect()->to('/visitEntry')->with('error', 'Visit not found.');
        }

        return view('editVisit', ['visit' => $visit, 'driver' => $visit]);
    }

    public function update($id)
    {
        if (!in_array((int) session()->get('role'), $this->adminLikeRoles(), true)) {
            return redirect()->to('/dashboard');
        }

        $visit = $this->getVisitDetails((int) $id);
        if (!$visit) {
            return redirect()->to('/visitEntry')->with('error', 'Visit not found.');
        }

        $validationRules = [
            'verification_method' => 'required|in_list[' . implode(',', self::VISIT_VERIFICATION_METHODS) . ']',
            'verification_reference' => 'permit_empty|max_length[120]',
            'visited_at' => 'required',
            'guest_count' => 'required|is_natural',
            'food_offered' => 'required|in_list[0,1]',
            'remarks' => 'permit_empty|max_length[1000]',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $foodOffered = (int) $this->request->getPost('food_offered') === 1;

        $cashIncentiveAmount = $this->decimalOrZero($visit['cash_incentive_amount'] ?? '0');
        $payload = [
            'verification_method' => (string) $this->request->getPost('verification_method'),
            'verification_reference' => $this->emptyToNull($this->request->getPost('verification_reference')),
            'visited_at' => date('Y-m-d H:i:s', strtotime((string) $this->request->getPost('visited_at'))),
            'guest_count' => (int) $this->request->getPost('guest_count'),
            'food_offered' => $foodOffered ? 1 : 0,
            'food_quantity' => null,
            'food_type' => null,
            'cash_incentive_amount' => $cashIncentiveAmount,
            'amount_paid' => '0.00',
            'remarks' => $this->emptyToNull($this->request->getPost('remarks')),
            'handled_by_user_id' => session()->get('user')['id'] ?? null,
            'incentive_given_by_user_id' => (float) $cashIncentiveAmount > 0 ? (session()->get('user')['id'] ?? null) : null,
        ];

        $visitModel = new VisitModel();
        $updated = $visitModel->update($id, $payload);

        if ($updated) {
            $visitBranchId = (int) ($visit['branch_id'] ?? $this->requireBranchId());
            (new IncentiveEngineService())->recomputeForVisitDate((int) $visit['driver_id'], (string) $visit['visited_at'], $visitBranchId);
            (new IncentiveEngineService())->recomputeForVisitDate((int) $visit['driver_id'], $payload['visited_at'], $visitBranchId);
            (new DriverLoyaltyService())->recomputeDriver((int) $visit['driver_id']);
            $this->logAudit('visit.updated', 'visit', (int) $id, $visit, $payload);
            return redirect()->to('/visitEntry')->with('success', 'Visit updated successfully.');
        }

        return redirect()->to('/visitEntry')->with('error', 'Failed to update visit.');
    }

    public function delete($id)
    {
        if (!in_array((int) session()->get('role'), $this->adminLikeRoles(), true)) {
            return redirect()->to('/dashboard');
        }

        $visitModel = new VisitModel();
        $visit = $visitModel->find((int) $id);
        if ($visitModel->delete($id)) {
            if ($visit) {
                (new IncentiveEngineService())->recomputeForVisitDate(
                    (int) $visit['driver_id'],
                    (string) $visit['visited_at'],
                (int) ($visit['branch_id'] ?? $this->requireBranchId())
                );
                (new DriverLoyaltyService())->recomputeDriver((int) $visit['driver_id']);
            }
            $this->logAudit('visit.deleted', 'visit', (int) $id, $visit, null);
            return redirect()->to('/visitEntry')->with('success', 'Visit deleted successfully.');
        }

        return redirect()->to('/visitEntry')->with('error', 'Failed to delete visit.');
    }

    private function findDriverForVisitSearch(string $searchTerm): ?array
    {
        $driver = (new DriverModel())
            ->withPrimaryVehicle()
            ->groupStart()
                ->where('drivers.mobile_number', $searchTerm)
                ->orWhere('drivers.whatsapp_number', $searchTerm)
                ->orWhere('drivers.license_number', $searchTerm)
                ->orWhere('drivers.id', ctype_digit($searchTerm) ? (int) $searchTerm : 0)
                ->orWhere('vehicles.vehicle_number', strtoupper($searchTerm))
            ->groupEnd()
            ->first();

        if (!$driver) {
            return null;
        }

        $driver['incentive_offered'] = $this->extractIncentiveOffered($driver['notes'] ?? null);
        $driver['recent_visit_summary'] = $this->getRecentVisitSummary((int) $driver['id']);

        return $driver;
    }

    private function getDriverDetails(int $driverId): ?array
    {
        $driver = (new DriverModel())
            ->select('drivers.*, drivers.full_name AS driver_name, vehicles.id AS vehicle_id, vehicles.vehicle_number, vehicles.vehicle_type')
            ->join('vehicles', 'vehicles.driver_id = drivers.id AND vehicles.is_primary = 1 AND vehicles.deleted_at IS NULL', 'left')
            ->where('drivers.id', $driverId)
            ->first();

        if (!$driver) {
            return null;
        }

        $driver['incentive_offered'] = $this->extractIncentiveOffered($driver['notes'] ?? null);
        return $driver;
    }

    private function getVisitListing(): array
    {
        $query = (new VisitModel())
            ->select('visits.id, branches.name AS branch_name, drivers.full_name AS driver_name, drivers.mobile_number, vehicles.vehicle_number, visits.guest_count, visits.food_offered, visits.cash_incentive_amount, visits.verification_method, visits.visited_at AS visit_date, visits.latitude, visits.longitude, visits.location_accuracy, visits.location_address, verifier.name AS verified_by_name, handler.name AS handled_by_name')
            ->join('branches', 'branches.id = visits.branch_id', 'left')
            ->join('drivers', 'drivers.id = visits.driver_id')
            ->join('vehicles', 'vehicles.id = visits.vehicle_id', 'left')
            ->join('users verifier', 'verifier.id = visits.verified_by_user_id', 'left')
            ->join('users handler', 'handler.id = visits.handled_by_user_id', 'left');

        $this->applyBranchScope($query, 'visits.branch_id');

        return $query->orderBy('visits.visited_at', 'DESC')->findAll();
    }

    private function getVisitDetails(int $visitId): ?array
    {
        return (new VisitModel())
            ->select('visits.*, visits.visited_at AS visit_date, drivers.full_name AS driver_name, drivers.mobile_number, vehicles.vehicle_number, vehicles.vehicle_type')
            ->join('drivers', 'drivers.id = visits.driver_id')
            ->join('vehicles', 'vehicles.id = visits.vehicle_id', 'left')
            ->where('visits.id', $visitId)
            ->first();
    }

    private function extractIncentiveOffered(?string $notes): string
    {
        if (!$notes) {
            return '';
        }

        $prefix = 'Incentive offered: ';
        if (str_starts_with($notes, $prefix)) {
            return substr($notes, strlen($prefix));
        }

        return $notes;
    }

    private function validateVisitFrequency(int $driverId, string $visitedAt, int $branchId): ?string
    {
        $visitModel = new VisitModel();
        $duplicateWindowStart = date('Y-m-d H:i:s', strtotime($visitedAt . ' -' . self::DUPLICATE_CHECKIN_HOURS . ' hours'));

        $recentVisit = $visitModel
            ->where('driver_id', $driverId)
            ->where('branch_id', $branchId)
            ->where('visited_at >=', $duplicateWindowStart)
            ->where('visited_at <=', $visitedAt)
            ->first();

        if ($recentVisit) {
            return 'This driver already has a visit logged within the last ' . self::DUPLICATE_CHECKIN_HOURS . ' hours.';
        }

        $dayStart = date('Y-m-d 00:00:00', strtotime($visitedAt));
        $dayEnd = date('Y-m-d 23:59:59', strtotime($visitedAt));
        $sameDayCount = $visitModel
            ->where('driver_id', $driverId)
            ->where('branch_id', $branchId)
            ->where('visited_at >=', $dayStart)
            ->where('visited_at <=', $dayEnd)
            ->countAllResults();

        if ($sameDayCount >= self::MAX_VISITS_PER_DAY) {
            return 'This driver already reached the daily visit limit of ' . self::MAX_VISITS_PER_DAY . ' visits.';
        }

        return null;
    }

    private function resolveVisitVehicle(int $driverId, array $driver): ?int
    {
        $vehicleModel = new VehicleModel();
        $vehicleNumber = strtoupper(trim((string) $this->request->getPost('vehicle_number')));
        $vehicleType = strtolower(trim((string) $this->request->getPost('vehicle_type')));
        $existingVehicleId = (int) ($driver['vehicle_id'] ?? 0);

        $vehicle = $vehicleModel
            ->where('vehicle_number', $vehicleNumber)
            ->where('deleted_at', null)
            ->first();

        if ($vehicle && (int) $vehicle['driver_id'] !== $driverId) {
            return null;
        }

        if ($vehicle) {
            $vehicleModel->update((int) $vehicle['id'], [
                'vehicle_type' => $vehicleType,
                'is_primary' => 1,
                'status' => 'active',
                'assigned_until' => null,
            ]);

            if ((int) $vehicle['id'] !== $existingVehicleId) {
                $this->demoteOtherPrimaryVehicles($driverId, (int) $vehicle['id']);
            }

            return (int) $vehicle['id'];
        }

        $vehicleModel->insert([
            'driver_id' => $driverId,
            'vehicle_number' => $vehicleNumber,
            'vehicle_type' => $vehicleType,
            'vehicle_brand' => null,
            'vehicle_model' => null,
            'vehicle_color' => null,
            'rc_number' => null,
            'rc_expiry_date' => null,
            'permit_number' => null,
            'permit_expiry_date' => null,
            'insurance_policy_number' => null,
            'insurance_expiry_date' => null,
            'is_primary' => 1,
            'status' => 'active',
            'assigned_from' => date('Y-m-d H:i:s'),
        ]);

        $newVehicleId = (int) $vehicleModel->getInsertID();
        $this->demoteOtherPrimaryVehicles($driverId, $newVehicleId);

        return $newVehicleId;
    }

    private function demoteOtherPrimaryVehicles(int $driverId, int $activeVehicleId): void
    {
        (new VehicleModel())
            ->where('driver_id', $driverId)
            ->where('id !=', $activeVehicleId)
            ->where('is_primary', 1)
            ->set([
                'is_primary' => 0,
                'assigned_until' => date('Y-m-d H:i:s'),
            ])
            ->update();
    }

    private function getRecentVisitSummary(int $driverId): array
    {
        $visitModel = new VisitModel();
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        $branchId = $this->requireBranchId();

        $latestVisit = $visitModel
            ->where('driver_id', $driverId)
            ->where('branch_id', $branchId)
            ->orderBy('visited_at', 'DESC')
            ->first();

        $todayCount = $visitModel
            ->where('driver_id', $driverId)
            ->where('branch_id', $branchId)
            ->where('visited_at >=', $todayStart)
            ->where('visited_at <=', $todayEnd)
            ->countAllResults();

        return [
            'latest_visit_at' => $latestVisit['visited_at'] ?? null,
            'today_count' => $todayCount,
        ];
    }

    private function decimalOrZero($value): string
    {
        $trimmed = trim((string) $value);
        if ($trimmed === '') {
            return '0.00';
        }

        return number_format((float) $trimmed, 2, '.', '');
    }

    private function emptyToNull($value): ?string
    {
        $trimmed = trim((string) $value);
        return $trimmed === '' ? null : $trimmed;
    }

    /**
     * Reads the browser-captured geolocation from the request and returns a
     * sanitised payload. Coordinates are only accepted as a valid pair within
     * real-world bounds; anything else is stored as NULL so the visit still
     * saves even when the device denies or fails to resolve a location.
     *
     * @return array{latitude: ?string, longitude: ?string, location_accuracy: ?string, location_captured_at: ?string, location_address: ?string}
     */
    private function resolveGeolocationPayload(): array
    {
        $empty = [
            'latitude' => null,
            'longitude' => null,
            'location_accuracy' => null,
            'location_captured_at' => null,
            'location_address' => null,
        ];

        $latRaw = $this->emptyToNull($this->request->getPost('latitude'));
        $lngRaw = $this->emptyToNull($this->request->getPost('longitude'));

        if ($latRaw === null || $lngRaw === null || !is_numeric($latRaw) || !is_numeric($lngRaw)) {
            return $empty;
        }

        $latitude = (float) $latRaw;
        $longitude = (float) $lngRaw;

        if ($latitude < -90.0 || $latitude > 90.0 || $longitude < -180.0 || $longitude > 180.0) {
            return $empty;
        }

        $accuracyRaw = $this->emptyToNull($this->request->getPost('location_accuracy'));
        $accuracy = ($accuracyRaw !== null && is_numeric($accuracyRaw) && (float) $accuracyRaw >= 0)
            ? number_format((float) $accuracyRaw, 2, '.', '')
            : null;

        // Prefer an address the browser already resolved; otherwise reverse-geocode server-side.
        $address = $this->emptyToNull($this->request->getPost('location_address'));
        if ($address !== null) {
            $address = mb_substr(strip_tags($address), 0, 255);
        } else {
            $address = (new GeocodingService())->reverseGeocode($latitude, $longitude);
        }

        return [
            'latitude' => number_format($latitude, 7, '.', ''),
            'longitude' => number_format($longitude, 7, '.', ''),
            'location_accuracy' => $accuracy,
            'location_captured_at' => date('Y-m-d H:i:s'),
            'location_address' => $address,
        ];
    }

    private function getVisitValidationRules(): array
    {
        return [
            'verification_method' => 'required|in_list[' . implode(',', self::VISIT_VERIFICATION_METHODS) . ']',
            'verification_reference' => 'permit_empty|max_length[120]',
            'visited_at' => 'required',
            'guest_count' => 'required|is_natural',
            'food_offered' => 'required|in_list[0,1]',
            'remarks' => 'permit_empty|max_length[1000]',
        ];
    }

    private function getInlineDriverValidationRules(): array
    {
        return [
            'full_name' => 'required|min_length[3]|max_length[150]',
            'mobile_number' => 'required|numeric|min_length[10]|max_length[20]|is_unique[drivers.mobile_number]',
            'whatsapp_number' => 'permit_empty|numeric|min_length[10]|max_length[20]|is_unique[drivers.whatsapp_number]',
            'license_number' => 'permit_empty|max_length[100]|is_unique[drivers.license_number]',
            'city' => 'permit_empty|max_length[100]',
            'state' => 'permit_empty|max_length[100]',
            'default_cash_incentive_amount' => 'required|decimal',
            'status' => 'permit_empty|in_list[active,blocked,duplicate,blacklisted]',
        ];
    }

    private function getInlineVehicleValidationRules(): array
    {
        return [
            'vehicle_number' => 'required|max_length[30]|is_unique[vehicles.vehicle_number]',
            'vehicle_type' => 'required|in_list[' . implode(',', self::VEHICLE_TYPES) . ']',
        ];
    }

    private function recordVehicleActivity(int $branchId, int $vehicleId, int $driverId, int $visitId, string $activityAt): void
    {
        (new VehicleBranchActivityModel())->insert([
            'branch_id' => $branchId,
            'vehicle_id' => $vehicleId,
            'driver_id' => $driverId,
            'visit_id' => $visitId,
            'activity_type' => 'visit',
            'activity_at' => $activityAt,
            'notes' => null,
            'created_by_user_id' => session()->get('user')['id'] ?? null,
        ]);
    }

    private function buildInlineDriverPayload(): array
    {
        $whatsappNumber = $this->emptyToNull($this->request->getPost('whatsapp_number'));

        return [
            'full_name' => trim((string) $this->request->getPost('full_name')),
            'mobile_number' => trim((string) $this->request->getPost('mobile_number')),
            'whatsapp_number' => $whatsappNumber,
            'whatsapp_opt_in' => $whatsappNumber !== null ? 1 : 0,
            'whatsapp_opt_in_at' => $whatsappNumber !== null ? date('Y-m-d H:i:s') : null,
            'license_number' => $this->emptyToNull($this->request->getPost('license_number')),
            'city' => $this->emptyToNull($this->request->getPost('city')),
            'state' => $this->emptyToNull($this->request->getPost('state')),
            'default_cash_incentive_amount' => $this->decimalOrZero($this->request->getPost('default_cash_incentive_amount') ?: '200'),
            'status' => (string) ($this->request->getPost('status') ?: 'active'),
            'registered_by' => session()->get('user')['id'] ?? null,
            'verified_at' => date('Y-m-d H:i:s'),
        ];
    }

    private function buildInlineVehiclePayload(int $driverId): array
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
            'is_primary' => 1,
            'status' => 'active',
            'assigned_from' => date('Y-m-d H:i:s'),
        ];
    }

    private function detectRegistrationDuplicates(): array
    {
        $driverModel = new DriverModel();
        $vehicleModel = new VehicleModel();
        $warnings = [];

        $mobileNumber = trim((string) $this->request->getPost('mobile_number'));
        $whatsappNumber = trim((string) $this->request->getPost('whatsapp_number'));
        $licenseNumber = trim((string) $this->request->getPost('license_number'));
        $vehicleNumber = strtoupper(trim((string) $this->request->getPost('vehicle_number')));

        if ($mobileNumber !== '' && $driverModel->where('mobile_number', $mobileNumber)->first()) {
            $warnings[] = 'Another driver already uses this mobile number.';
        }

        if ($whatsappNumber !== '' && $driverModel->where('whatsapp_number', $whatsappNumber)->first()) {
            $warnings[] = 'Another driver already uses this WhatsApp number.';
        }

        if ($licenseNumber !== '' && $driverModel->where('license_number', $licenseNumber)->first()) {
            $warnings[] = 'Another driver already uses this license number.';
        }

        if ($vehicleNumber !== '' && $vehicleModel->where('vehicle_number', $vehicleNumber)->first()) {
            $warnings[] = 'Another driver already uses this vehicle number.';
        }

        return $warnings;
    }

}
