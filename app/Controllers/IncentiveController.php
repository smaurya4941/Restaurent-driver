<?php

namespace App\Controllers;

use App\Models\IncentiveRuleModel;
use App\Models\DriverBonusAwardModel;
use App\Services\IncentiveEngineService;

class IncentiveController extends BaseController
{
    public function legacyIndex()
    {
        return redirect()->to('/bonus-rules');
    }

    public function legacyToggle($id)
    {
        return redirect()->to('/bonus-rules/' . (int) $id . '/toggle');
    }

    public function index()
    {
        if ($redirect = $this->authorize($this->adminLikeRoles())) {
            return $redirect;
        }

        return view('incentiveRules', [
            'rules' => (new IncentiveEngineService())->getCurrentRuleListing(),
        ]);
    }

    public function driverBonuses()
    {
        if ($redirect = $this->authorize($this->reportingRoles())) {
            return $redirect;
        }

        $year = (int) ($this->request->getGet('year') ?: date('Y'));
        $month = (int) ($this->request->getGet('month') ?: date('n'));
        $search = trim((string) $this->request->getGet('search_input'));

        return view('driverBonuses', [
            'bonuses' => (new IncentiveEngineService())->getMonthlyBonusReport($year, $month, $search),
            'year' => max(2000, min(2100, $year)),
            'month' => max(1, min(12, $month)),
            'searchInput' => $search,
            'canManagePayouts' => true,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->authorize($this->adminLikeRoles())) {
            return $redirect;
        }

        $validationRules = [
            'name' => 'required|max_length[150]',
            'visit_threshold' => 'required|is_natural',
            'bonus_value' => 'required|decimal',
            'effective_from' => 'required|valid_date',
            'effective_to' => 'permit_empty|valid_date',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $effectiveFrom = (string) $this->request->getPost('effective_from');
        $effectiveTo = trim((string) $this->request->getPost('effective_to'));

        if ($effectiveTo !== '' && strtotime($effectiveTo) < strtotime($effectiveFrom)) {
            return redirect()->back()->withInput()->with('error', 'Effective to date must be after effective from date.');
        }

        $ruleModel = new IncentiveRuleModel();
        $branchId = $this->requireBranchId();

        $duplicate = $ruleModel
            ->where('branch_id', $branchId)
            ->where('visit_threshold', (int) $this->request->getPost('visit_threshold'))
            ->where('effective_from', $effectiveFrom)
            ->where('effective_to', $effectiveTo === '' ? null : $effectiveTo)
            ->first();

        if ($duplicate) {
            return redirect()->back()->withInput()->with('error', 'A rule with the same threshold and effective period already exists.');
        }

        $ruleModel->insert([
            'branch_id' => $branchId,
            'name' => trim((string) $this->request->getPost('name')),
            'visit_threshold' => (int) $this->request->getPost('visit_threshold'),
            'bonus_type' => 'percentage',
            'bonus_value' => number_format((float) $this->request->getPost('bonus_value'), 2, '.', ''),
            'effective_from' => $effectiveFrom,
            'effective_to' => $effectiveTo === '' ? null : $effectiveTo,
            'is_active' => (int) $this->request->getPost('is_active') === 1 ? 1 : 0,
        ]);

        $ruleId = (int) $ruleModel->getInsertID();
        $createdRule = $ruleModel->find($ruleId);
        $this->logAudit('incentive_rule.created', 'incentive_rule', $ruleId, null, $createdRule);
        if ($createdRule) {
            (new IncentiveEngineService())->recomputeForRule($createdRule);
        }

        return redirect()->to('/bonus-rules')->with('success', 'Bonus rule saved successfully.');
    }

    public function toggle($id)
    {
        if ($redirect = $this->authorize($this->adminLikeRoles())) {
            return $redirect;
        }

        $ruleModel = new IncentiveRuleModel();
        $rule = $ruleModel->find((int) $id);

        if (!$rule || !$this->branchContext->canAccessBranch(isset($rule['branch_id']) ? (int) $rule['branch_id'] : null)) {
            return redirect()->to('/bonus-rules')->with('error', 'Bonus rule not found.');
        }

        $ruleModel->update((int) $id, ['is_active' => (int) $rule['is_active'] === 1 ? 0 : 1]);
        $updatedRule = $ruleModel->find((int) $id);
        $this->logAudit('incentive_rule.toggled', 'incentive_rule', (int) $id, $rule, $updatedRule);
        if ($updatedRule) {
            (new IncentiveEngineService())->recomputeForRule($updatedRule);
        }

        return redirect()->to('/bonus-rules')->with('success', 'Bonus rule status updated.');
    }

    public function approveAward($awardId)
    {
        if ($redirect = $this->authorize($this->reportingRoles())) {
            return $redirect;
        }

        $awardModel = new DriverBonusAwardModel();
        $award = $awardModel->find((int) $awardId);
        if (
            !$award
            || (float) ($award['bonus_amount'] ?? 0) <= 0
            || !$this->branchContext->canAccessBranch(isset($award['branch_id']) ? (int) $award['branch_id'] : null)
        ) {
            return redirect()->back()->with('error', 'Eligible bonus award not found.');
        }

        $payload = [
            'payout_status' => 'approved',
            'approved_by_user_id' => $this->currentUser()['id'] ?? null,
            'approved_at' => date('Y-m-d H:i:s'),
        ];

        $awardModel->update((int) $awardId, $payload);
        $this->logAudit('driver_bonus_award.approved', 'driver_bonus_award', (int) $awardId, $award, $awardModel->find((int) $awardId));

        return redirect()->back()->with('success', 'Bonus approved successfully.');
    }

    public function payAward($awardId)
    {
        if ($redirect = $this->authorize($this->reportingRoles())) {
            return $redirect;
        }

        $awardModel = new DriverBonusAwardModel();
        $award = $awardModel->find((int) $awardId);
        if (
            !$award
            || (float) ($award['bonus_amount'] ?? 0) <= 0
            || !$this->branchContext->canAccessBranch(isset($award['branch_id']) ? (int) $award['branch_id'] : null)
        ) {
            return redirect()->back()->with('error', 'Eligible bonus award not found.');
        }

        $payload = [
            'payout_status' => 'paid',
            'approved_by_user_id' => $award['approved_by_user_id'] ?: ($this->currentUser()['id'] ?? null),
            'approved_at' => $award['approved_at'] ?: date('Y-m-d H:i:s'),
            'paid_by_user_id' => $this->currentUser()['id'] ?? null,
            'paid_at' => date('Y-m-d H:i:s'),
            'payout_reference' => $this->emptyToNull($this->request->getPost('payout_reference')),
            'payout_notes' => $this->emptyToNull($this->request->getPost('payout_notes')),
        ];

        $awardModel->update((int) $awardId, $payload);
        $this->logAudit('driver_bonus_award.paid', 'driver_bonus_award', (int) $awardId, $award, $awardModel->find((int) $awardId));

        return redirect()->back()->with('success', 'Bonus marked as paid.');
    }

    private function emptyToNull($value): ?string
    {
        $trimmed = trim((string) $value);
        return $trimmed === '' ? null : $trimmed;
    }
}
