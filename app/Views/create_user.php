<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = 'Create Employee';
$pageSubtitle = 'Add a new admin, accountant, or front desk account.';
$pageEyebrow = 'Administration';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Users', 'url' => base_url('user_list')],
    ['label' => 'Create User', 'active' => true],
];
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card ops-card mb-4">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Employee Access Setup</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('create_user_handler') ?>" method="POST">
                                <?= csrf_field() ?>
                                <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #1A1C1C; background: #F8F9FA; padding: 12px; border-radius: 4px; border: 1px solid #E0E0E0; margin-bottom: 24px;">
                                    <strong>Role guide:</strong> Admin manages everything, Accountant sees reports, and Security handles visits and driver verification.
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" required placeholder="John Doe">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Mobile Number</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" required placeholder="9876543210">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="role">Role</label>
                                            <select class="form-control" id="role" name="role" required>
                                                <?php foreach (($roles ?? []) as $role): ?>
                                                    <option value="<?= $role['id'] ?>"><?= esc(ucwords(str_replace('_', ' ', $role['name']))) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php if (count($branches ?? []) > 1): ?>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="branch_id">Branch</label>
                                                <select class="form-control" id="branch_id" name="branch_id">
                                                    <option value="">Select branch</option>
                                                    <?php foreach (($branches ?? []) as $branch): ?>
                                                        <option value="<?= (int) $branch['id'] ?>"><?= esc($branch['name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php elseif (count($branches ?? []) === 1): ?>
                                        <input type="hidden" name="branch_id" value="<?= (int) $branches[0]['id'] ?>">
                                    <?php endif; ?>
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label for="status">Employee Status</label>
                                            <select class="form-control" id="status" name="status" required>
                                                <?php foreach (($statusOptions ?? []) as $status): ?>
                                                    <option value="<?= esc($status) ?>" <?= $status === 'active' ? 'selected' : '' ?>><?= esc(ucfirst($status)) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer" style="background: #F8F9FA; border-top: 1px solid #E0E0E0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                                <div style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #4F4255;">Inactive and disabled users cannot sign in.</div>
                                <div style="display: flex; gap: 10px;">
                                    <button type="submit" class="btn btn-primary-enterprise">Create User</button>
                                    <a href="<?= base_url('user_list') ?>" class="btn btn-outline-enterprise">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* =========================================
   ENTERPRISE LAYOUT & CARD
========================================= */
.ops-card {
    background: #FFFFFF;
    border-radius: 4px;
    border: 1px solid #E0E0E0;
    box-shadow: none;
    margin-bottom: 24px;
}
.ops-toolbar {
    background: #F5F5F5;
    padding: 16px 20px;
    border-bottom: 1px solid #E0E0E0;
    border-radius: 4px 4px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}
.ops-toolbar .card-title {
    font-family: 'Hanken Grotesk', sans-serif;
    font-weight: 600;
    color: #1A1C1C;
    font-size: 18px;
}

/* =========================================
   FORM INPUTS
========================================= */
.form-group label {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    font-weight: 500;
    color: #4F4255;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 6px;
}
.form-control {
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    padding: 10px 12px;
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    color: #1A1C1C;
    background: #FFFFFF;
    box-shadow: none !important;
    transition: all 0.2s ease;
    height: auto;
}
.form-control:focus {
    border-color: #A600FF;
    outline: 0;
}

/* =========================================
   BUTTONS
========================================= */
.btn-primary-enterprise {
    background: #A600FF;
    color: #FFFFFF;
    border: none;
    border-radius: 4px;
    font-family: 'Hanken Grotesk', sans-serif;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 20px;
    transition: background 0.2s;
    text-decoration: none;
}
.btn-primary-enterprise:hover {
    background: #8300CA;
    color: #FFFFFF;
}
.btn-outline-enterprise {
    background: transparent;
    color: #1A1C1C;
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    font-family: 'Hanken Grotesk', sans-serif;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 20px;
    transition: all 0.2s;
    text-decoration: none;
}
.btn-outline-enterprise:hover {
    background: #F5F5F5;
    border-color: #1A1C1C;
}
</style>

<?php include 'app/Views/templates/footer.php'; ?>
