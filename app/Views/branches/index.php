<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = 'Branches';
$pageSubtitle = 'Manage Hawa Hawai locations across India.';
$pageEyebrow = 'Super Admin';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Branches', 'active' => true],
];
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="row">
                <div class="col-lg-7">
                    <div class="card ops-card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Active Branches</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (($branches ?? []) as $branch): ?>
                                        <tr>
                                            <td><?= esc($branch['name']) ?></td>
                                            <td><?= esc($branch['branch_code']) ?></td>
                                            <td><?= esc($branch['city']) ?></td>
                                            <td><?= esc($branch['state']) ?></td>
                                            <td><?= esc(ucfirst($branch['status'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card ops-card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Add Branch</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('branches') ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="form-group">
                                    <label>Branch name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Branch code</label>
                                    <input type="text" name="branch_code" class="form-control" placeholder="HH-DEL" required>
                                </div>
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="city" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>State</label>
                                    <input type="text" name="state" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea name="address" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Create branch</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/templates/footer.php'; ?>
