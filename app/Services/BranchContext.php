<?php

namespace App\Services;

use App\Models\BranchModel;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class BranchContext
{
    public const ROLE_SUPER_ADMIN = 5;

    public function isSuperAdmin(): bool
    {
        return (int) session()->get('role') === self::ROLE_SUPER_ADMIN;
    }

    public function getUserBranchId(): ?int
    {
        $branchId = session()->get('user')['branch_id'] ?? null;

        return $branchId !== null && $branchId !== '' ? (int) $branchId : null;
    }

  /**
   * Active branch for scoping queries. Super admins may view all (null) or one branch.
   */
    public function getScopeBranchId(): ?int
    {
        if ($this->isSuperAdmin()) {
            $active = session()->get('active_branch_id');
            if ($active === null || $active === '' || $active === 'all') {
                return null;
            }

            return (int) $active;
        }

        return $this->getUserBranchId();
    }

    public function requireBranchId(): int
    {
        $branchId = $this->getScopeBranchId() ?? $this->getUserBranchId();
        if ($branchId !== null && $branchId > 0) {
            return $branchId;
        }

        $first = (new BranchModel())->getActiveBranches()[0]['id'] ?? null;
        if ($first !== null) {
            return (int) $first;
        }

        throw new \RuntimeException('No active branch is configured.');
    }

    public function canAccessBranch(?int $branchId): bool
    {
        if ($branchId === null) {
            return $this->isSuperAdmin();
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->getUserBranchId() === $branchId;
    }

    public function applyScope(Model|BaseBuilder $target, string $column = 'branch_id'): Model|BaseBuilder
    {
        $branchId = $this->getScopeBranchId();
        if ($branchId === null) {
            return $target;
        }

        if ($target instanceof Model) {
            return $target->where($column, $branchId);
        }

        return $target->where($column, $branchId);
    }

    public function getActiveBranchLabel(): string
    {
        if (!$this->isSuperAdmin()) {
            $branchId = $this->getUserBranchId();
            if ($branchId === null) {
                return 'Unassigned branch';
            }

            $branch = (new BranchModel())->find($branchId);

            return $branch ? (string) $branch['name'] : 'Branch #' . $branchId;
        }

        $scopeId = $this->getScopeBranchId();
        if ($scopeId === null) {
            return 'All branches';
        }

        $branch = (new BranchModel())->find($scopeId);

        return $branch ? (string) $branch['name'] : 'Branch #' . $scopeId;
    }

    public function setActiveBranch(?int $branchId): void
    {
        if (!$this->isSuperAdmin()) {
            return;
        }

        if ($branchId === null || $branchId <= 0) {
            session()->set('active_branch_id', 'all');
            return;
        }

        session()->set('active_branch_id', $branchId);
    }
}
