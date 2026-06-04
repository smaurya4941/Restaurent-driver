<?php

namespace App\Models;

use App\Services\BranchContext;
use CodeIgniter\Model;

abstract class BranchScopedModel extends Model
{
    protected string $branchScopeColumn = 'branch_id';
    protected bool $branchScopeEnabled = true;

    protected function initialize(): void
    {
        $this->beforeFind[] = 'applyBranchFindScope';
        $this->beforeDelete[] = 'applyBranchDeleteScope';
        $this->beforeInsert[] = 'applyBranchInsertScope';
        $this->beforeUpdate[] = 'applyBranchUpdateScope';
    }

    protected function applyBranchFindScope(array $data): array
    {
        if (!$this->shouldApplyBranchScope()) {
            return $data;
        }

        $branchId = $this->branchContext()->getScopeBranchId();
        if ($branchId !== null) {
            $this->where($this->table . '.' . $this->branchScopeColumn, $branchId);
        }

        return $data;
    }

    protected function applyBranchDeleteScope(array $data): array
    {
        if (!$this->shouldApplyBranchScope()) {
            return $data;
        }

        $branchId = $this->branchContext()->getScopeBranchId();
        if ($branchId !== null) {
            $this->where($this->table . '.' . $this->branchScopeColumn, $branchId);
        }

        return $data;
    }

    protected function applyBranchUpdateScope(array $data): array
    {
        if (!$this->shouldApplyBranchScope()) {
            return $data;
        }

        $branchId = $this->branchContext()->getScopeBranchId();
        if ($branchId !== null) {
            $this->where($this->table . '.' . $this->branchScopeColumn, $branchId);
        }

        return $data;
    }

    protected function applyBranchInsertScope(array $data): array
    {
        if (!$this->shouldApplyBranchScope()) {
            return $data;
        }

        $context = $this->branchContext();
        if ($context->isSuperAdmin()) {
            return $data;
        }

        $branchId = $context->getUserBranchId();
        if ($branchId === null) {
            return $data;
        }

        if (isset($data['data']) && is_array($data['data'])) {
            $data['data'][$this->branchScopeColumn] = $branchId;
        }

        return $data;
    }

    public function withoutBranchScope(): static
    {
        $this->branchScopeEnabled = false;

        return $this;
    }

    private function shouldApplyBranchScope(): bool
    {
        return $this->branchScopeEnabled && session()->get('logged_in');
    }

    private function branchContext(): BranchContext
    {
        return new BranchContext();
    }
}
