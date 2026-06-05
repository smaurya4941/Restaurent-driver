<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$pageTitle = 'Expenses';
$breadcrumbs = [['label' => 'Finance'], ['label' => 'Expenses', 'active' => true]];
$canManage = in_array((int) session()->get('role'), [5, 1], true);
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>
    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <?php if ($canManage): ?>
                <div class="card ops-card mb-3">
                    <div class="card-header"><h3 class="card-title">Add Expense</h3></div>
                    <form action="<?= base_url('expenses') ?>" method="post">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <label>Category</label>
                                    <select name="category" class="form-control" required>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= esc($category) ?>"><?= esc(ucwords(str_replace('_', ' ', $category))) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Amount</label>
                                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required>
                                </div>
                                <div class="col-md-2">
                                    <label>Date</label>
                                    <input type="date" name="expense_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-2">
                                    <label>Vendor</label>
                                    <input type="text" name="vendor_name" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label>Mode</label>
                                    <input type="text" name="payment_mode" class="form-control" placeholder="Cash / UPI / Bank">
                                </div>
                                <div class="col-md-2">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <?php foreach ($statuses as $status): ?>
                                            <option value="<?= esc($status) ?>"><?= esc(ucwords($status)) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mt-3">
                                    <label>Reference</label>
                                    <input type="text" name="reference_number" class="form-control">
                                </div>
                                <div class="col-md-8 mt-3">
                                    <label>Notes</label>
                                    <input type="text" name="notes" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save mr-1"></i> Save Expense</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <div class="card ops-card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Branch</th>
                                <th>Category</th>
                                <th>Vendor</th>
                                <th class="text-right">Amount</th>
                                <th>Status</th>
                                <th>Reference</th>
                                <?php if ($canManage): ?><th>Action</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($expenses as $expense): ?>
                                <tr>
                                    <td><?= esc($expense['expense_date']) ?></td>
                                    <td><?= esc($expense['branch_name'] ?? '') ?></td>
                                    <td><?= esc(ucwords(str_replace('_', ' ', $expense['category'] ?? ''))) ?></td>
                                    <td><?= esc($expense['vendor_name'] ?? '') ?></td>
                                    <td class="text-right">₹<?= number_format((float) ($expense['amount'] ?? 0), 2) ?></td>
                                    <td><span class="badge badge-secondary"><?= esc(ucwords($expense['status'] ?? '')) ?></span></td>
                                    <td><?= esc($expense['reference_number'] ?? '') ?></td>
                                    <?php if ($canManage): ?>
                                        <td>
                                            <form action="<?= base_url('expenses/' . (int) $expense['id'] . '/status') ?>" method="post" class="d-flex" style="gap:0.35rem;">
                                                <select name="status" class="form-control form-control-sm">
                                                    <?php foreach ($statuses as $status): ?>
                                                        <option value="<?= esc($status) ?>" <?= ($expense['status'] ?? '') === $status ? 'selected' : '' ?>><?= esc(ucwords($status)) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button class="btn btn-sm btn-outline-primary">Update</button>
                                            </form>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($expenses === []): ?>
                                <tr><td colspan="<?= $canManage ? 8 : 7 ?>" class="text-center text-muted py-4">No expenses found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/templates/footer.php'; ?>
