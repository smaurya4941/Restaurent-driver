
<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>

<div class="content-wrapper ops-page-shell">
    <!-- PAGE HEADER -->
    <div class="content-header ops-toolbar pb-0 mb-4" style="border-bottom: none; background: transparent;">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-8">
                    <h1 class="m-0" style="font-family: 'Hanken Grotesk', sans-serif; font-weight: 700; color: #1A1C1C; letter-spacing: -0.02em;">
                        <i class="fa-solid fa-code-branch text-primary mr-2" style="font-size: 24px; color: #A600FF !important;"></i>
                        <?= lang('App.branch_management') ?>
                    </h1>
                    <p class="text-muted mb-0 mt-1" style="font-family: 'Inter', sans-serif; font-size: 14px;"><?= lang('App.manage_branches') ?></p>
                </div>
                <div class="col-sm-4 text-right">
                    <button class="btn btn-primary-enterprise px-4" data-toggle="modal" data-target="#addBranchModal">
                        <i class="fa-solid fa-plus mr-2"></i> <?= lang('App.add_branch') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <!-- STATS -->
            <div class="row mb-4">
                <div class="col-md-3 col-6 mb-3">
                    <div class="card ops-card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted" style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase;"><?= lang('App.total_branches') ?></h6>
                            <h2 class="font-weight-bold" style="font-family: 'Inter', sans-serif; font-size: 28px; color: #1A1C1C;">
                                <?php echo count($branches ?? []) ?>
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-6 mb-3">
                    <div class="card ops-card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-success" style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase;"><?= lang('App.active') ?></h6>
                            <h2 class="font-weight-bold" style="font-family: 'Inter', sans-serif; font-size: 28px; color: #10B981;">
                                <?php echo count(array_filter($branches, fn($b) => $b['status'] === 'active')) ?>
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-6 mb-3">
                    <div class="card ops-card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-danger" style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase;"><?= lang('App.inactive') ?></h6>
                            <h2 class="font-weight-bold" style="font-family: 'Inter', sans-serif; font-size: 28px; color: #F43F5E;">
                                <?php echo count(array_filter($branches, fn($b) => $b['status'] === 'inactive')) ?>
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-6 mb-3">
                    <div class="card ops-card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-primary" style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase;; color: #A600FF !important;"><?= lang('App.admins') ?></h6>
                            <h2 class="font-weight-bold" style="font-family: 'Inter', sans-serif; font-size: 28px; color: #1A1C1C;">
                                <?php echo count($branchAdmins['all'] ?? []) ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BRANCH CARDS -->
            <div class="row">

                <?php foreach (($branches ?? []) as $branch): ?>

                    <?php
                        $assignedAdmins = $branchAdmins['byBranch'][(int) $branch['id']] ?? [];
                    ?>

                    <div class="col-xl-4 col-md-6 col-12 mb-4">
                        <div class="card ops-card h-100 border-0 shadow-sm">
                            <div class="card-body d-flex flex-column">

                                <!-- TOP -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="font-weight-bold mb-1" style="font-family: 'Hanken Grotesk', sans-serif; color: #1A1C1C;">
                                            <i class="fa-solid fa-building mr-2" style="color: #A600FF;"></i>
                                            <?php echo esc($branch['name']) ?>
                                        </h5>
                                        <span class="badge-enterprise-role bg-light text-dark border">
                                            <?php echo esc($branch['branch_code']) ?>
                                        </span>
                                    </div>

                                    <?php if ($branch['status'] === 'active'): ?>
                                        <span class="badge-enterprise-role" style="background: #10B981; color: #FFFFFF;">
                                            <?= lang('App.active') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-enterprise-role" style="background: #F43F5E; color: #FFFFFF;">
                                            <?= lang('App.inactive') ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- LOCATION -->
                                <div class="mb-3">
                                    <p class="mb-1 text-dark" style="font-family: 'Inter', sans-serif; font-size: 14px;">
                                        <i class="fa-solid fa-location-dot text-danger mr-2"></i>
                                        <?php echo esc($branch['city']) ?>, <?php echo esc($branch['state']) ?>
                                    </p>
                                    <small class="text-muted" style="font-size: 13px;">
                                        <?php echo esc($branch['address']) ?>
                                    </small>
                                </div>

                                <!-- PHONE -->
                                <div class="mb-3" style="font-family: 'JetBrains Mono', monospace; font-size: 13px;">
                                    <i class="fa-solid fa-phone text-success mr-2"></i>
                                    <?php echo esc($branch['phone'] ?? 'N/A') ?>
                                </div>

                                <!-- ADMINS -->
                                <div class="mb-4">
                                    <h6 class="font-weight-bold mb-2" style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase; color: #4F4255;">
                                        <?= lang('App.assigned_admins') ?>
                                    </h6>
                                    <?php if ($assignedAdmins): ?>
                                        <?php foreach ($assignedAdmins as $admin): ?>
                                            <span class="badge-enterprise-role bg-light text-dark border mr-1 mb-1">
                                                <i class="fa-solid fa-user-shield mr-1"></i>
                                                <?php echo esc($admin['name']) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="badge-enterprise-role bg-warning text-dark">
                                            <?= lang('App.no_admin_assigned') ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- ACTIONS -->
                                <div class="mt-auto">
                                    <div class="d-flex flex-wrap">
                                        <button class="btn btn-outline-enterprise btn-sm mr-2 mb-2" data-toggle="modal" data-target="#editBranchModal<?php echo $branch['id'] ?>">
                                            <i class="fa-solid fa-pen mr-1"></i> <?= lang('App.edit') ?>
                                        </button>
                                        <button class="btn btn-outline-enterprise btn-sm mr-2 mb-2" data-toggle="modal" data-target="#adminModal<?php echo $branch['id'] ?>">
                                            <i class="fa-solid fa-user-shield mr-1"></i> <?= lang('App.admins') ?>
                                        </button>

                                        <form action="<?php echo base_url('branches/' . $branch['id'] . '/toggle-status') ?>" method="post">
                                            <?php echo csrf_field() ?>
                                            <?php if ($branch['status'] === 'active'): ?>
                                                <button class="btn btn-outline-danger btn-sm mb-2" style="font-family: 'JetBrains Mono', monospace; font-size: 13px;">
                                                    <i class="fa-solid fa-ban mr-1"></i> <?= lang('App.deactivate') ?>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-outline-success btn-sm mb-2" style="font-family: 'JetBrains Mono', monospace; font-size: 13px;">
                                                    <i class="fa-solid fa-check mr-1"></i> <?= lang('App.activate') ?>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- EDIT MODAL -->
                    <div class="modal fade"
                         id="editBranchModal<?php echo $branch['id'] ?>"
                         tabindex="-1">

                        <div class="modal-dialog modal-lg">
                            <div class="modal-content rounded-4 border-0">

                                <form action="<?php echo base_url('branches/' . $branch['id']) ?>"
                                      method="post">

                                    <?php echo csrf_field() ?>

                                    <div class="modal-header ops-toolbar" style="background: #A600FF; color: #FFFFFF; border-radius: 4px 4px 0 0;">
                                        <h5 class="modal-title font-weight-bold" style="font-family: 'Hanken Grotesk', sans-serif;">
                                            <?= lang('App.edit_branch') ?>
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase; color: #4F4255;"><?= lang('App.branch_name') ?></label>
                                                    <input type="text" name="name" class="form-control" style="font-family: 'Inter', sans-serif; font-size: 13px;" value="<?php echo esc($branch['name']) ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase; color: #4F4255;"><?= lang('App.branch_code') ?></label>
                                                    <input type="text" name="branch_code" class="form-control" style="font-family: 'Inter', sans-serif; font-size: 13px;" value="<?php echo esc($branch['branch_code']) ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase; color: #4F4255;"><?= lang('App.city') ?></label>
                                                    <input type="text" name="city" class="form-control" style="font-family: 'Inter', sans-serif; font-size: 13px;" value="<?php echo esc($branch['city']) ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase; color: #4F4255;"><?= lang('App.state') ?></label>
                                                    <input type="text" name="state" class="form-control" style="font-family: 'Inter', sans-serif; font-size: 13px;" value="<?php echo esc($branch['state']) ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase; color: #4F4255;"><?= lang('App.address') ?></label>
                                                    <textarea name="address" class="form-control" style="font-family: 'Inter', sans-serif; font-size: 13px;" rows="3"><?php echo esc($branch['address']) ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase; color: #4F4255;"><?= lang('App.phone') ?></label>
                                                    <input type="text" name="phone" class="form-control" style="font-family: 'Inter', sans-serif; font-size: 13px;" value="<?php echo esc($branch['phone']) ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase; color: #4F4255;"><?= lang('App.status') ?></label>
                                                    <select name="status" class="form-control" style="font-family: 'Inter', sans-serif; font-size: 13px;">
                                                        <option value="active" <?php echo $branch['status'] === 'active' ? 'selected' : '' ?>><?= lang('App.active') ?></option>
                                                        <option value="inactive" <?php echo $branch['status'] === 'inactive' ? 'selected' : '' ?>><?= lang('App.inactive') ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary-enterprise px-4" style="font-size: 13px;">
                                            <i class="fa-solid fa-floppy-disk mr-2"></i> <?= lang('App.update_branch') ?>
                                        </button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>

                    <!-- ADMIN MODAL -->
                    <div class="modal fade"
                         id="adminModal<?php echo $branch['id'] ?>"
                         tabindex="-1">

                        <div class="modal-dialog">
                            <div class="modal-content rounded-4 border-0">

                                <form action="<?php echo base_url('branches/' . $branch['id'] . '/admins') ?>"
                                      method="post">

                                    <?php echo csrf_field() ?>

                                    <div class="modal-header ops-toolbar" style="background: #A600FF; color: #FFFFFF; border-radius: 4px 4px 0 0;">
                                        <h5 class="modal-title font-weight-bold" style="font-family: 'Hanken Grotesk', sans-serif;">
                                            <?= lang('App.assign_admins') ?>
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    <!-- <div class="modal-body">

                                        <div class="form-group">
                                            <label>Select Admins</label>

                                            <select name="admin_ids[]"
                                                    class="form-control"
                                                    multiple
                                                    size="8">

                                                <?php foreach (($branchAdmins['all'] ?? []) as $admin): ?>

                                                    <option value="<?php echo $admin['id'] ?>">
                                                        <?php echo esc($admin['name']) ?>
                                                        (<?php echo esc($admin['email']) ?>)
                                                    </option>

                                                <?php endforeach; ?>

                                            </select>
                                        </div>

                                    </div> -->


<div class="modal-body">
    <label class="font-weight-bold mb-3 d-block" style="font-family: 'Hanken Grotesk', sans-serif; font-size: 15px;">
        <?= lang('App.select_branch_admins') ?>
    </label>

    <?php
        $assignedAdmins = $branchAdmins['byBranch'][(int) $branch['id']] ?? [];

        $assignedAdminIds = array_map(
            static fn($admin): int => (int) $admin['id'],
            $assignedAdmins
        );
    ?>

    <div style="max-height:400px; overflow-y:auto;">

        <?php foreach (($branchAdmins['all'] ?? []) as $admin): ?>

            <?php
                $isChecked = in_array(
                    (int) $admin['id'],
                    $assignedAdminIds,
                    true
                );
            ?>

<label class="d-block mb-3 admin-select-card" style="cursor:pointer;"> <input type="checkbox" name="admin_ids[]" value="<?php echo (int) $admin['id'] ?>" <?php echo $isChecked ? 'checked' : '' ?> class="admin-checkbox" style="display:none;"> <div class="d-flex align-items-center p-3 rounded shadow-sm border admin-card-ui" style=" transition:0.2s; background: <?php echo $isChecked ? '#F3E8FF' : '#ffffff' ?>; border-color: <?php echo $isChecked ? '#A600FF' : '#dee2e6' ?> !important; "> <!-- Avatar --> <div class="mr-3 d-flex align-items-center justify-content-center rounded-circle" style=" width:50px; height:50px; background:linear-gradient(135deg,#A600FF,#4F4255); color:white; font-weight:bold; font-size:18px; flex-shrink:0; "> <?php echo strtoupper(substr($admin['name'], 0, 1)) ?> </div> <!-- Info --> <div class="flex-grow-1"> <h6 class="mb-1 font-weight-bold" style="font-family: 'Inter', sans-serif;"> <?php echo esc($admin['name']) ?> </h6> <small class="text-muted" style="font-family: 'JetBrains Mono', monospace; font-size: 11px;"> <?php echo esc($admin['phone'] ?? '') ?> </small> </div> <!-- Status --> <div class="ml-3"> <span class="admin-status badge-enterprise-role <?php echo $isChecked ? 'bg-primary' : 'bg-light text-dark border' ?>"> <?php echo $isChecked ? '<i class="fa-solid fa-check"></i> ' . lang('App.selected') : lang('App.select') ?> </span> </div> </div> </label>

        <?php endforeach; ?>

    </div>

</div>


                                    <div class="modal-footer">
                                        <button class="btn btn-primary-enterprise" style="font-size: 13px;">
                                            <?= lang('App.save_admins') ?>
                                        </button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>

            </div>

        </div>
    </section>
</div>

<!-- ADD BRANCH MODAL -->
<div class="modal fade"
     id="addBranchModal"
     tabindex="-1">

    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 rounded-4">

            <form action="<?php echo base_url('branches') ?>"
                  method="post">

                <?php echo csrf_field() ?>

                <div class="modal-header ops-toolbar" style="background: #A600FF; color: #FFFFFF; border-radius: 4px 4px 0 0;">
                    <h5 class="modal-title font-weight-bold" style="font-family: 'Hanken Grotesk', sans-serif;">
                        <?= lang('App.add_new_branch') ?>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase; color: #4F4255;"><?= lang('App.branch_name') ?></label>
                                                    <input type="text" name="name" class="form-control" style="font-family: 'Inter', sans-serif; font-size: 13px;" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase; color: #4F4255;"><?= lang('App.branch_code') ?></label>
                                                    <input type="text" name="branch_code" class="form-control" style="font-family: 'Inter', sans-serif; font-size: 13px;" placeholder="HH-DEL" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase; color: #4F4255;"><?= lang('App.city') ?></label>
                                                    <input type="text" name="city" class="form-control" style="font-family: 'Inter', sans-serif; font-size: 13px;" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase; color: #4F4255;"><?= lang('App.state') ?></label>
                                                    <input type="text" name="state" class="form-control" style="font-family: 'Inter', sans-serif; font-size: 13px;" required>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase; color: #4F4255;"><?= lang('App.address') ?></label>
                                                    <textarea name="address" class="form-control" style="font-family: 'Inter', sans-serif; font-size: 13px;" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase; color: #4F4255;"><?= lang('App.phone') ?></label>
                                                    <input type="text" name="phone" class="form-control" style="font-family: 'Inter', sans-serif; font-size: 13px;">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase; color: #4F4255;"><?= lang('App.status') ?></label>
                                                    <select name="status" class="form-control" style="font-family: 'Inter', sans-serif; font-size: 13px;">
                                                        <option value="active"><?= lang('App.active') ?></option>
                                                        <option value="inactive"><?= lang('App.inactive') ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary-enterprise px-4" style="font-size: 13px;">
                        <i class="fa-solid fa-plus mr-2"></i> <?= lang('App.create_branch') ?>
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script> document.querySelectorAll('.admin-select-card').forEach(card => { const checkbox = card.querySelector('.admin-checkbox'); const ui = card.querySelector('.admin-card-ui'); const status = card.querySelector('.admin-status'); card.addEventListener('click', function () { setTimeout(() => { if (checkbox.checked) { ui.style.background = '#F3E8FF'; ui.style.borderColor = '#A600FF'; status.className = 'admin-status badge-enterprise-role bg-primary'; status.innerHTML = '<i class="fa-solid fa-check"></i> <?= lang('App.selected') ?>'; } else { ui.style.background = '#ffffff'; ui.style.borderColor = '#dee2e6'; status.className = 'admin-status badge-enterprise-role bg-light text-dark border'; status.innerHTML = '<?= lang('App.select') ?>'; } }, 10); }); }); </script>

<?php include 'app/Views/templates/footer.php'; ?>
