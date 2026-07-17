<?php

namespace App\Controllers;

use App\Models\DriverModel;
use App\Models\DriverMonthlySummaryModel;
use App\Models\VehicleModel;
use App\Models\VisitModel;
use App\Services\DriverRegistrationNotifier;
use App\Services\IncentiveEngineService;

class DriverController extends BaseController
{
    private const DRIVER_STATUSES = ['active', 'blocked', 'duplicate', 'blacklisted'];
    private const VEHICLE_TYPES = ['car 5 seat', 'car 7 seat', 'bus', 'traveller'];

    public function index()
    {
        if ($redirect = $this->authorize($this->adminLikeRoles())) {
            return $redirect;
        }

        return view('driverEntry', [
            'drivers' => $this->getDriverListing(),
            'user_role' => (int) session()->get('role'),
        ]);
    }

    public function create()
    {
        if ($redirect = $this->authorize($this->adminLikeRoles())) {
            return $redirect;
        }

        return view('addDriver', [
            'statusOptions' => self::DRIVER_STATUSES,
            'vehicleTypeOptions' => self::VEHICLE_TYPES,
            'duplicateWarnings' => [],
        ]);
    }

    public function store()
    {
        if ($redirect = $this->authorize($this->adminLikeRoles())) {
            return $redirect;
        }

        $validationRules = array_merge($this->getDriverValidationRules(), $this->getVehicleValidationRules());

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $duplicateWarnings = $this->detectDuplicates();
        if ($duplicateWarnings !== []) {
            return redirect()->back()->withInput()->with('duplicate_warnings', $duplicateWarnings);
        }

        $driverModel = new DriverModel();
        $db = \Config\Database::connect();

        $db->transStart();

        $driverModel->insert($this->buildDriverPayload());
        $driverId = $driverModel->getInsertID();

        (new VehicleModel())->insert($this->buildVehiclePayload($driverId));

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Failed to save driver details.');
        }

        $this->logAudit('driver.created', 'driver', $driverId, null, $driverModel->find($driverId));
        $this->queueWelcomeMessage($driverId);

        return redirect()->to('/drivers')->with('success', 'Driver added successfully.');
    }

    public function edit($id)
    {
        if ($redirect = $this->authorize($this->adminLikeRoles())) {
            return $redirect;
        }

        $driver = $this->getDriverDetails((int) $id);
        if (!$driver) {
            return redirect()->to('/drivers')->with('error', 'Driver not found.');
        }

        return view('editDriver', [
            'driver' => $driver,
            'statusOptions' => self::DRIVER_STATUSES,
            'vehicleTypeOptions' => self::VEHICLE_TYPES,
            'vehicleHistory' => (new VehicleModel())->getVehicleHistoryForDriver((int) $id),
            'duplicateWarnings' => [],
        ]);
    }

    public function show($id)
    {
        if ($redirect = $this->authorize($this->adminLikeRoles())) {
            return $redirect;
        }

        $driver = $this->getDriverDetails((int) $id);
        if (!$driver) {
            return redirect()->to('/drivers')->with('error', 'Driver not found.');
        }

        return view('driverProfile', [
            'driver' => $driver,
            'visitHistory' => $this->getDriverVisitHistory((int) $id),
            'monthlySummaries' => (new IncentiveEngineService())->getDriverMonthlySummaries((int) $id),
            'canManagePayouts' => in_array((int) session()->get('role'), $this->reportingRoles(), true),
        ]);
    }

    public function viewUpload(string $type, string $filename)
    {
        if ($redirect = $this->authorize($this->adminLikeRoles())) {
            return $redirect;
        }

        $allowedDirectories = [
            'photos' => 'drivers/photos',
            'licenses' => 'drivers/licenses',
        ];

        if (!isset($allowedDirectories[$type]) || basename($filename) !== $filename) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $baseDirectory = realpath(WRITEPATH . 'uploads/' . $allowedDirectories[$type]);
        if ($baseDirectory === false) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $filePath = realpath($baseDirectory . DIRECTORY_SEPARATOR . $filename);

        if ($filePath === false || strpos($filePath, $baseDirectory . DIRECTORY_SEPARATOR) !== 0 || !is_file($filePath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setHeader('Content-Type', mime_content_type($filePath) ?: 'application/octet-stream')
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($filePath) . '"')
            ->setBody(file_get_contents($filePath));
    }

    public function update($id)
    {
        if ($redirect = $this->authorize($this->adminLikeRoles())) {
            return $redirect;
        }

        $driver = $this->getDriverDetails((int) $id);
        if (!$driver) {
            return redirect()->to('/drivers')->with('error', 'Driver not found.');
        }

        $validationRules = $this->getDriverValidationRules((int) $id);
        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $duplicateWarnings = $this->detectDuplicates((int) $id, (int) ($driver['vehicle_id'] ?? 0));
        if ($duplicateWarnings !== []) {
            return redirect()->back()->withInput()->with('duplicate_warnings', $duplicateWarnings);
        }

        $driverModel = new DriverModel();
        $db = \Config\Database::connect();

        $db->transStart();

        $oldDriver = $driverModel->find((int) $id);
        $driverModel->update((int) $id, $this->buildDriverPayload($driver));

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Failed to update driver details.');
        }

        $this->logAudit('driver.updated', 'driver', (int) $id, $oldDriver, $driverModel->find((int) $id));
        return redirect()->to('/drivers')->with('success', 'Driver updated successfully.');
    }

    public function delete($id)
    {
        if ($redirect = $this->authorize($this->adminLikeRoles())) {
            return $redirect;
        }

        $driverModel = new DriverModel();
        $driver = $driverModel->find((int) $id);
        if ($driverModel->delete((int) $id)) {
            if ($driver) {
                $this->logAudit('driver.deleted', 'driver', (int) $id, $driver, null);
            }
            return redirect()->to('/drivers')->with('success', 'Driver deleted successfully.');
        }

        return redirect()->to('/drivers')->with('error', 'Failed to delete driver.');
    }

    public function approveBonus($summaryId)
    {
        if ($redirect = $this->authorize($this->reportingRoles())) {
            return $redirect;
        }

        $summaryModel = new DriverMonthlySummaryModel();
        $summary = $summaryModel->find((int) $summaryId);
        if (
            !$summary
            || !$this->branchContext->canAccessBranch(isset($summary['branch_id']) ? (int) $summary['branch_id'] : null)
        ) {
            return redirect()->back()->with('error', 'Monthly bonus summary not found.');
        }

        if ((float) ($summary['bonus_amount'] ?? 0) <= 0) {
            return redirect()->back()->with('error', 'Only eligible bonus summaries can be approved.');
        }

        $payload = [
            'payout_status' => 'approved',
            'approved_by_user_id' => $this->currentUser()['id'] ?? null,
            'approved_at' => date('Y-m-d H:i:s'),
        ];

        $summaryModel->update((int) $summaryId, $payload);
        $this->logAudit('driver_bonus.approved', 'driver_monthly_summary', (int) $summaryId, $summary, $summaryModel->find((int) $summaryId));

        return redirect()->back()->with('success', 'Monthly bonus approved successfully.');
    }

    public function markBonusPaid($summaryId)
    {
        if ($redirect = $this->authorize($this->reportingRoles())) {
            return $redirect;
        }

        $summaryModel = new DriverMonthlySummaryModel();
        $summary = $summaryModel->find((int) $summaryId);
        if (
            !$summary
            || !$this->branchContext->canAccessBranch(isset($summary['branch_id']) ? (int) $summary['branch_id'] : null)
        ) {
            return redirect()->back()->with('error', 'Monthly bonus summary not found.');
        }

        if ((float) ($summary['bonus_amount'] ?? 0) <= 0) {
            return redirect()->back()->with('error', 'Only eligible bonus summaries can be paid.');
        }

        $payload = [
            'payout_status' => 'paid',
            'approved_by_user_id' => $summary['approved_by_user_id'] ?: ($this->currentUser()['id'] ?? null),
            'approved_at' => $summary['approved_at'] ?: date('Y-m-d H:i:s'),
            'paid_by_user_id' => $this->currentUser()['id'] ?? null,
            'paid_at' => date('Y-m-d H:i:s'),
            'payout_reference' => $this->emptyToNull($this->request->getPost('payout_reference')),
            'payout_notes' => $this->emptyToNull($this->request->getPost('payout_notes')),
        ];

        $summaryModel->update((int) $summaryId, $payload);
        $this->logAudit('driver_bonus.paid', 'driver_monthly_summary', (int) $summaryId, $summary, $summaryModel->find((int) $summaryId));

        return redirect()->back()->with('success', 'Monthly bonus marked as paid.');
    }

    /**
     * Queue the automated welcome WhatsApp message for a newly registered driver.
     * Runs after the driver + vehicle have been committed. Any failure here is
     * logged inside the notifier and must never affect the registration result.
     */
    private function queueWelcomeMessage(int $driverId): void
    {
        $driver = (new DriverModel())->find($driverId);
        if (!$driver) {
            return;
        }

        $vehicle = (new VehicleModel())
            ->where('driver_id', $driverId)
            ->where('is_primary', 1)
            ->first();

        (new DriverRegistrationNotifier())->sendWelcomeMessage(
            $driver,
            $vehicle ?: null,
            $this->branchContext->getActiveBranchLabel()
        );
    }

    private function getDriverValidationRules(?int $driverId = null): array
    {
        $driverUniqueSuffix = $driverId ? ',id,' . $driverId : '';

        $rules = [
            'full_name' => 'required|min_length[3]|max_length[150]',
            'mobile_number' => 'required|numeric|min_length[10]|max_length[20]|is_unique[drivers.mobile_number' . $driverUniqueSuffix . ']',
            'whatsapp_number' => 'permit_empty|numeric|min_length[10]|max_length[20]|is_unique[drivers.whatsapp_number' . $driverUniqueSuffix . ']',
            'whatsapp_opt_in' => 'permit_empty|in_list[0,1]',
            'whatsapp_opt_out_reason' => 'permit_empty|max_length[255]',
            'license_number' => 'permit_empty|max_length[100]|is_unique[drivers.license_number' . $driverUniqueSuffix . ']',
            'address' => 'permit_empty|max_length[1000]',
            'city' => 'permit_empty|max_length[100]',
            'state' => 'permit_empty|max_length[100]',
            'default_cash_incentive_amount' => 'required|decimal',
            'status' => 'required|in_list[' . implode(',', self::DRIVER_STATUSES) . ']',
        ];

        if ($this->hasUploadedFile('photo_path')) {
            $rules['photo_path'] = 'max_size[photo_path,5120]|is_image[photo_path]';
        }

        if ($this->hasUploadedFile('license_photo_path')) {
            $rules['license_photo_path'] = 'max_size[license_photo_path,5120]|is_image[license_photo_path]';
        }

        return $rules;
    }

    private function buildDriverPayload(?array $existingDriver = null): array
    {
        $photoPath = $this->storeUpload('photo_path', 'drivers/photos', $existingDriver['photo_path'] ?? null);
        $licensePhotoPath = $this->storeUpload('license_photo_path', 'drivers/licenses', $existingDriver['license_photo_path'] ?? null);
        $whatsAppOptIn = (int) ($this->request->getPost('whatsapp_opt_in') ?? 0) === 1 ? 1 : 0;
        $existingOptIn = (int) ($existingDriver['whatsapp_opt_in'] ?? 0);
        $optInAt = $existingDriver['whatsapp_opt_in_at'] ?? null;
        $optOutAt = $existingDriver['whatsapp_opt_out_at'] ?? null;

        if ($whatsAppOptIn === 1 && $existingOptIn !== 1) {
            $optInAt = date('Y-m-d H:i:s');
            $optOutAt = null;
        }

        if ($whatsAppOptIn === 0 && $existingOptIn === 1) {
            $optOutAt = date('Y-m-d H:i:s');
        }

        return [
            'full_name' => trim((string) $this->request->getPost('full_name')),
            'mobile_number' => trim((string) $this->request->getPost('mobile_number')),
            'whatsapp_number' => $this->emptyToNull($this->request->getPost('whatsapp_number')),
            'whatsapp_opt_in' => $whatsAppOptIn,
            'whatsapp_opt_in_at' => $optInAt,
            'whatsapp_opt_out_at' => $whatsAppOptIn === 1 ? null : $optOutAt,
            'whatsapp_opt_out_reason' => $whatsAppOptIn === 1 ? null : $this->emptyToNull($this->request->getPost('whatsapp_opt_out_reason')),
            'photo_path' => $photoPath,
            'license_photo_path' => $licensePhotoPath,
            'license_number' => $this->emptyToNull($this->request->getPost('license_number')),
            'address' => $this->emptyToNull($this->request->getPost('address')),
            'city' => $this->emptyToNull($this->request->getPost('city')),
            'state' => $this->emptyToNull($this->request->getPost('state')),
            'notes' => $this->emptyToNull($this->request->getPost('notes')),
            'default_cash_incentive_amount' => number_format((float) $this->request->getPost('default_cash_incentive_amount'), 2, '.', ''),
            'status' => (string) $this->request->getPost('status'),
            'registered_by' => session()->get('user')['id'] ?? null,
            'verified_at' => $this->request->getPost('verified_at') ? date('Y-m-d H:i:s') : null,
        ];
    }

    private function buildVehiclePayload(int $driverId): array
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
            'assigned_until' => null,
        ];
    }

    private function getVehicleValidationRules(): array
    {
        return [
            'vehicle_number' => 'required|max_length[30]|is_unique[vehicles.vehicle_number]',
            'vehicle_type' => 'required|in_list[' . implode(',', self::VEHICLE_TYPES) . ']',
        ];
    }

    private function detectDuplicates(?int $driverId = null, ?int $vehicleId = null): array
    {
        $driverModel = new DriverModel();
        $vehicleModel = new VehicleModel();
        $warnings = [];

        $mobileNumber = trim((string) $this->request->getPost('mobile_number'));
        $whatsappNumber = trim((string) $this->request->getPost('whatsapp_number'));
        $licenseNumber = trim((string) $this->request->getPost('license_number'));
        $vehicleNumber = strtoupper(trim((string) $this->request->getPost('vehicle_number')));

        if ($mobileNumber !== '') {
            $query = $driverModel->where('mobile_number', $mobileNumber);
            if ($driverId) {
                $query->where('id !=', $driverId);
            }
            if ($query->first()) {
                $warnings[] = 'Another driver already uses this mobile number.';
            }
        }

        if ($whatsappNumber !== '') {
            $query = $driverModel->where('whatsapp_number', $whatsappNumber);
            if ($driverId) {
                $query->where('id !=', $driverId);
            }
            if ($query->first()) {
                $warnings[] = 'Another driver already uses this WhatsApp number.';
            }
        }

        if ($licenseNumber !== '') {
            $query = $driverModel->where('license_number', $licenseNumber);
            if ($driverId) {
                $query->where('id !=', $driverId);
            }
            if ($query->first()) {
                $warnings[] = 'Another driver already uses this license number.';
            }
        }

        if ($vehicleNumber !== '') {
            $query = $vehicleModel->where('vehicle_number', $vehicleNumber);
            if ($vehicleId) {
                $query->where('id !=', $vehicleId);
            }
            if ($query->first()) {
                $warnings[] = 'Another driver already uses this vehicle number.';
            }
        }

        return $warnings;
    }

    private function storeUpload(string $field, string $directory, ?string $existingPath = null): ?string
    {
        $file = $this->request->getFile($field);

        if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return $existingPath;
        }

        if (!$file->isValid()) {
            return $existingPath;
        }

        $targetDirectory = WRITEPATH . 'uploads/' . $directory;
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }

        $newName = $file->getRandomName();
        $file->move($targetDirectory, $newName, true);

        return 'uploads/' . $directory . '/' . $newName;
    }

    // private function getDriverListing(): array
    // {
    //     return (new DriverModel())
    //         ->select('drivers.id, drivers.full_name AS driver_name, drivers.mobile_number, drivers.whatsapp_number, drivers.license_number, drivers.status, vehicles.id AS vehicle_id, vehicles.vehicle_number, vehicles.vehicle_type')
    //         ->join('vehicles', 'vehicles.driver_id = drivers.id AND vehicles.is_primary = 1', 'left')
    //         ->orderBy('drivers.id', 'DESC')
    //         ->findAll();
    // }

    //getting driver listing with soft delete check
    private function getDriverListing(): array
    {
        return (new DriverModel())
            ->withPrimaryVehicle()
            ->where('drivers.deleted_at', null)
            ->orderBy('drivers.id', 'DESC')
            ->findAll();
    }

    private function getDriverDetails(int $driverId): ?array
    {
        return (new DriverModel())
            ->withPrimaryVehicle()
            ->where('drivers.id', $driverId)
            ->first();
    }

    private function getDriverVisitHistory(int $driverId): array
    {
        $query = (new VisitModel())
            ->select('visits.*, vehicles.vehicle_number, users.name AS incentive_given_by_name')
            ->join('vehicles', 'vehicles.id = visits.vehicle_id', 'left')
            ->join('users', 'users.id = visits.incentive_given_by_user_id', 'left')
            ->where('visits.driver_id', $driverId)
            ->orderBy('visits.visited_at', 'DESC');

        $this->applyBranchScope($query, 'visits.branch_id');

        return $query->findAll();
    }

    private function emptyToNull($value): ?string
    {
        $trimmed = trim((string) $value);
        return $trimmed === '' ? null : $trimmed;
    }

    private function hasUploadedFile(string $field): bool
    {
        $file = $this->request->getFile($field);

        return $file !== null && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE;
    }
}
