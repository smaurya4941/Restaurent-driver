<?php
$role = (int) session()->get('role');
$isSuperAdmin = $role === 5;
$branches = (new \App\Models\BranchModel())->getActiveBranches();
$activeBranchId = session()->get('active_branch_id');
$branchLabel = (new \App\Services\BranchContext())->getActiveBranchLabel();
?>

<?php if ($isSuperAdmin && $branches !== []): ?>
    <li class="nav-item mr-2">
        <form action="<?= base_url('branches/switch') ?>" method="post" class="d-flex align-items-center bg-white border rounded px-2" style="height:40px;">
            <?= csrf_field() ?>
            <i class="fa-solid fa-location-dot text-primary mr-2"></i>
            <select name="branch_id" class="border-0 bg-transparent" style="outline:none; max-width:180px;" onchange="this.form.submit()">
                <option value="all" <?= ($activeBranchId === 'all' || $activeBranchId === null || $activeBranchId === '') ? 'selected' : '' ?>>All branches</option>
                <?php foreach ($branches as $branch): ?>
                    <option value="<?= (int) $branch['id'] ?>" <?= (int) $activeBranchId === (int) $branch['id'] ? 'selected' : '' ?>>
                        <?= esc($branch['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </li>
<?php elseif (!$isSuperAdmin): ?>
    <li class="nav-item mr-2 d-none d-md-flex align-items-center">
        <span class="badge badge-light border px-3 py-2">
            <i class="fa-solid fa-location-dot mr-1"></i><?= esc($branchLabel) ?>
        </span>
    </li>
<?php endif; ?>
