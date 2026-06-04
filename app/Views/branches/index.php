
<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>

<div class="content-wrapper bg-light">
    <section class="content pt-4">
        <div class="container-fluid">

            <!-- PAGE HEADER -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                <div>
                    <h2 class="font-weight-bold mb-1">
                        <i class="fa-solid fa-code-branch text-warning"></i>
                        Branch Management
                    </h2>
                    <p class="text-muted mb-0">
                        Manage all Hawa Hawai branches across India
                    </p>
                </div>

                <button class="btn btn-warning shadow-sm px-4"
                        data-toggle="modal"
                        data-target="#addBranchModal">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Add Branch
                </button>
            </div>

            <!-- STATS -->
            <div class="row mb-4">

                <div class="col-md-3 col-6 mb-3">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <h6 class="text-muted">Total Branches</h6>
                            <h2 class="font-weight-bold">
                                <?php echo count($branches ?? []) ?>
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-6 mb-3">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <h6 class="text-success">Active</h6>
                            <h2 class="font-weight-bold">
                                <?php echo count(array_filter($branches, fn($b) => $b['status'] === 'active')) ?>
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-6 mb-3">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <h6 class="text-danger">Inactive</h6>
                            <h2 class="font-weight-bold">
                                <?php echo count(array_filter($branches, fn($b) => $b['status'] === 'inactive')) ?>
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-6 mb-3">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <h6 class="text-primary">Admins</h6>
                            <h2 class="font-weight-bold">
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

                        <div class="card border-0 shadow-sm rounded-4 h-100">

                            <div class="card-body d-flex flex-column">

                                <!-- TOP -->
                                <div class="d-flex justify-content-between align-items-start mb-3">

                                    <div>
                                        <h5 class="font-weight-bold mb-1">
                                            <i class="fa-solid fa-building text-warning mr-2"></i>
                                            <?php echo esc($branch['name']) ?>
                                        </h5>

                                        <span class="badge badge-light border">
                                            <?php echo esc($branch['branch_code']) ?>
                                        </span>
                                    </div>

                                    <?php if ($branch['status'] === 'active'): ?>
                                        <span class="badge badge-success px-3 py-2">
                                            Active
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-danger px-3 py-2">
                                            Inactive
                                        </span>
                                    <?php endif; ?>

                                </div>

                                <!-- LOCATION -->
                                <div class="mb-3">
                                    <p class="mb-1 text-dark">
                                        <i class="fa-solid fa-location-dot text-danger mr-2"></i>
                                        <?php echo esc($branch['city']) ?>,
                                        <?php echo esc($branch['state']) ?>
                                    </p>

                                    <small class="text-muted">
                                        <?php echo esc($branch['address']) ?>
                                    </small>
                                </div>

                                <!-- PHONE -->
                                <div class="mb-3">
                                    <i class="fa-solid fa-phone text-success mr-2"></i>
                                    <?php echo esc($branch['phone'] ?? 'N/A') ?>
                                </div>

                                <!-- ADMINS -->
                                <div class="mb-4">
                                    <h6 class="font-weight-bold mb-2">
                                        Assigned Admins
                                    </h6>

                                    <?php if ($assignedAdmins): ?>

                                        <?php foreach ($assignedAdmins as $admin): ?>
                                            <span class="badge badge-light border px-3 py-2 mr-1 mb-1">
                                                <i class="fa-solid fa-user-shield mr-1"></i>
                                                <?php echo esc($admin['name']) ?>
                                            </span>
                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <span class="badge badge-warning px-3 py-2">
                                            No Admin Assigned
                                        </span>

                                    <?php endif; ?>
                                </div>

                                <!-- ACTIONS -->
                                <div class="mt-auto">

                                    <div class="d-flex flex-wrap">

                                        <button class="btn btn-warning btn-sm mr-2 mb-2"
                                                data-toggle="modal"
                                                data-target="#editBranchModal<?php echo $branch['id'] ?>">
                                            <i class="fa-solid fa-pen mr-1"></i>
                                            Edit
                                        </button>

                                        <button class="btn btn-primary btn-sm mr-2 mb-2"
                                                data-toggle="modal"
                                                data-target="#adminModal<?php echo $branch['id'] ?>">
                                            <i class="fa-solid fa-user-shield mr-1"></i>
                                            Admins
                                        </button>

                                        <form action="<?php echo base_url('branches/' . $branch['id'] . '/toggle-status') ?>"
                                              method="post">

                                            <?php echo csrf_field() ?>

                                            <?php if ($branch['status'] === 'active'): ?>

                                                <button class="btn btn-outline-danger btn-sm mb-2">
                                                    <i class="fa-solid fa-ban mr-1"></i>
                                                    Deactivate
                                                </button>

                                            <?php else: ?>

                                                <button class="btn btn-outline-success btn-sm mb-2">
                                                    <i class="fa-solid fa-check mr-1"></i>
                                                    Activate
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

                                    <div class="modal-header bg-warning">
                                        <h5 class="modal-title font-weight-bold">
                                            Edit Branch
                                        </h5>

                                        <button type="button"
                                                class="close"
                                                data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">

                                        <div class="row">

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Branch Name</label>
                                                    <input type="text"
                                                           name="name"
                                                           class="form-control"
                                                           value="<?php echo esc($branch['name']) ?>"
                                                           required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Branch Code</label>
                                                    <input type="text"
                                                           name="branch_code"
                                                           class="form-control"
                                                           value="<?php echo esc($branch['branch_code']) ?>"
                                                           required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>City</label>
                                                    <input type="text"
                                                           name="city"
                                                           class="form-control"
                                                           value="<?php echo esc($branch['city']) ?>"
                                                           required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>State</label>
                                                    <input type="text"
                                                           name="state"
                                                           class="form-control"
                                                           value="<?php echo esc($branch['state']) ?>"
                                                           required>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Address</label>
                                                    <textarea name="address"
                                                              class="form-control"
                                                              rows="3"><?php echo esc($branch['address']) ?></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Phone</label>
                                                    <input type="text"
                                                           name="phone"
                                                           class="form-control"
                                                           value="<?php echo esc($branch['phone']) ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status"
                                                            class="form-control">

                                                        <option value="active"
                                                            <?php echo $branch['status'] === 'active' ? 'selected' : '' ?>>
                                                            Active
                                                        </option>

                                                        <option value="inactive"
                                                            <?php echo $branch['status'] === 'inactive' ? 'selected' : '' ?>>
                                                            Inactive
                                                        </option>

                                                    </select>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-warning px-4">
                                            <i class="fa-solid fa-floppy-disk mr-2"></i>
                                            Update Branch
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

                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">
                                            Assign Admins
                                        </h5>

                                        <button type="button"
                                                class="close text-white"
                                                data-dismiss="modal">
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

    <label class="font-weight-bold mb-3 d-block">
        Select Branch Admins
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

<label class="d-block mb-3 admin-select-card" style="cursor:pointer;"> <input type="checkbox" name="admin_ids[]" value="<?php echo (int) $admin['id'] ?>" <?php echo $isChecked ? 'checked' : '' ?> class="admin-checkbox" style="display:none;"> <div class="d-flex align-items-center p-3 rounded shadow-sm border admin-card-ui" style=" transition:0.2s; background: <?php echo $isChecked ? '#fff7e6' : '#ffffff' ?>; border-color: <?php echo $isChecked ? '#f59e0b' : '#dee2e6' ?> !important; "> <!-- Avatar --> <div class="mr-3 d-flex align-items-center justify-content-center rounded-circle" style=" width:50px; height:50px; background:linear-gradient(135deg,#f59e0b,#ff7a00); color:white; font-weight:bold; font-size:18px; flex-shrink:0; "> <?php echo strtoupper(substr($admin['name'], 0, 1)) ?> </div> <!-- Info --> <div class="flex-grow-1"> <h6 class="mb-1 font-weight-bold"> <?php echo esc($admin['name']) ?> </h6> <small class="text-muted"> <?php echo esc($admin['email']) ?> </small> </div> <!-- Status --> <div class="ml-3"> <span class="admin-status badge <?php echo $isChecked ? 'badge-warning' : 'badge-light border' ?> px-3 py-2"> <?php echo $isChecked ? '<i class="fa-solid fa-check"></i> Selected' : 'Select' ?> </span> </div> </div> </label>

        <?php endforeach; ?>

    </div>

</div>


                                    <div class="modal-footer">
                                        <button class="btn btn-primary">
                                            Save Admins
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

                <div class="modal-header bg-warning">
                    <h5 class="modal-title font-weight-bold">
                        Add New Branch
                    </h5>

                    <button type="button"
                            class="close"
                            data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Branch Name</label>
                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Branch Code</label>
                                <input type="text"
                                       name="branch_code"
                                       class="form-control"
                                       placeholder="HH-DEL"
                                       required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>City</label>
                                <input type="text"
                                       name="city"
                                       class="form-control"
                                       required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>State</label>
                                <input type="text"
                                       name="state"
                                       class="form-control"
                                       required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address"
                                          class="form-control"
                                          rows="3"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text"
                                       name="phone"
                                       class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>

                                <select name="status"
                                        class="form-control">

                                    <option value="active">
                                        Active
                                    </option>

                                    <option value="inactive">
                                        Inactive
                                    </option>

                                </select>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-warning px-4">
                        <i class="fa-solid fa-plus mr-2"></i>
                        Create Branch
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script> document.querySelectorAll('.admin-select-card').forEach(card => { const checkbox = card.querySelector('.admin-checkbox'); const ui = card.querySelector('.admin-card-ui'); const status = card.querySelector('.admin-status'); card.addEventListener('click', function () { setTimeout(() => { if (checkbox.checked) { ui.style.background = '#fff7e6'; ui.style.borderColor = '#f59e0b'; status.className = 'admin-status badge badge-warning px-3 py-2'; status.innerHTML = '<i class="fa-solid fa-check"></i> Selected'; } else { ui.style.background = '#ffffff'; ui.style.borderColor = '#dee2e6'; status.className = 'admin-status badge badge-light border px-3 py-2'; status.innerHTML = 'Select'; } }, 10); }); }); </script>

<?php include 'app/Views/templates/footer.php'; ?>
