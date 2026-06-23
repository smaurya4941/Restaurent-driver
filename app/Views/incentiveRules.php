<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>

<?php
$pageTitle = lang('App.bonus_rules');
$pageSubtitle = lang('App.bonus_rules_desc');
$pageEyebrow = lang('App.rewards_engine');
$breadcrumbs = [
    ['label' => lang('App.home'), 'url' => base_url('dashboard')],
    ['label' => lang('App.bonus_rules'), 'active' => true],
];
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card ops-card mb-4">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;"><?= lang('App.add_bonus_rule') ?></h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('bonus-rules') ?>" method="post">
                                <div class="form-group">
                                    <label for="name"><?= lang('App.rule_label') ?></label>
                                    <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name')) ?>" placeholder="<?= lang('App.example_bonus_plan') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="visit_threshold"><?= lang('App.min_monthly_visits') ?></label>
                                    <input type="number" min="0" name="visit_threshold" id="visit_threshold" class="form-control" value="<?= esc(old('visit_threshold')) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="bonus_value"><?= lang('App.bonus_percentage') ?></label>
                                    <input type="number" step="0.01" min="0" name="bonus_value" id="bonus_value" class="form-control" value="<?= esc(old('bonus_value')) ?>" placeholder="10.00" required>
                                </div>
                                <div class="form-group">
                                    <label for="effective_from"><?= lang('App.effective_from') ?></label>
                                    <input type="date" name="effective_from" id="effective_from" class="form-control" value="<?= esc(old('effective_from')) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="effective_to"><?= lang('App.effective_to') ?></label>
                                    <input type="date" name="effective_to" id="effective_to" class="form-control" value="<?= esc(old('effective_to')) ?>">
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #4F4255; margin-top: 6px;"><?= lang('App.leave_empty_bonus_active') ?></div>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="is_active"><?= lang('App.status') ?></label>
                                    <select name="is_active" id="is_active" class="form-control">
                                        <option value="1" <?= old('is_active', '1') === '1' ? 'selected' : '' ?>><?= lang('App.active') ?></option>
                                        <option value="0" <?= old('is_active') === '0' ? 'selected' : '' ?>><?= lang('App.inactive') ?></option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary-enterprise w-100"><?= lang('App.save_bonus_rule') ?></button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card ops-card mb-4">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;"><?= lang('App.configured_bonus_rules') ?></h3>
                            </div>
                        </div>
                        <div class="card-body ops-table-wrap p-0">
                            <div class="table-responsive">
                                <table class="table table-modern mb-0">
                                    <thead>
                                        <tr>
                                            <th><?= lang('App.version') ?></th>
                                            <th><?= lang('App.threshold') ?></th>
                                            <th><?= lang('App.bonus_percent') ?></th>
                                            <th><?= lang('App.effective_from') ?></th>
                                            <th><?= lang('App.effective_to') ?></th>
                                            <th><?= lang('App.status') ?></th>
                                            <th><?= lang('App.action') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rules as $rule): ?>
                                            <tr>
                                                <td data-label="Version">
                                                    <div style="font-weight: 600; color: #1A1C1C; font-size: 14px;">
                                                        <?= esc($rule['name']) ?>
                                                    </div>
                                                </td>
                                                <td data-label="Threshold">
                                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 13px; color: #1A1C1C;">
                                                        <?= esc((string) $rule['visit_threshold']) ?> visits
                                                    </div>
                                                </td>
                                                <td data-label="Bonus %">
                                                    <div class="badge-enterprise-role" style="background: #F3E8FF; color: #A600FF;">
                                                        <?= esc(number_format((float) $rule['bonus_value'], 2)) ?>%
                                                    </div>
                                                </td>
                                                <td data-label="Effective From">
                                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #4F4255;">
                                                        <?= esc($rule['effective_from'] ?: '-') ?>
                                                    </div>
                                                </td>
                                                <td data-label="Effective To">
                                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #4F4255;">
                                                        <?= esc($rule['effective_to'] ?: lang('App.open')) ?>
                                                    </div>
                                                </td>
                                                <td data-label="Status">
                                                    <span class="badge-enterprise-role" style="background: <?= (int) $rule['is_active'] === 1 ? '#10B981' : '#E0E0E0' ?>; color: <?= (int) $rule['is_active'] === 1 ? '#FFFFFF' : '#1A1C1C' ?>;">
                                                        <?= (int) $rule['is_active'] === 1 ? lang('App.active') : lang('App.inactive') ?>
                                                    </span>
                                                </td>
                                                <td data-label="Action">
                                                    <a href="<?= base_url('bonus-rules/' . $rule['id'] . '/toggle') ?>" class="btn btn-sm btn-outline-enterprise py-1 px-2" style="font-size: 12px;">
                                                        <?= (int) $rule['is_active'] === 1 ? lang('App.deactivate') : lang('App.activate') ?>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($rules)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4" style="font-family: 'Inter', sans-serif; font-size: 14px;">
                                                    <?= lang('App.no_bonus_rules_configured') ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
.ops-toolbar .text-muted {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    color: #4F4255;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-top: 4px;
}
.badge-enterprise-role {
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    font-weight: 500;
    padding: 4px 8px;
    background: #1A1C1C;
    color: #FFFFFF;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    display: inline-block;
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
textarea.form-control {
    min-height: 80px;
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

/* =========================================
   TABLE MODERN
========================================= */
.table-modern {
    width: 100%;
    border-collapse: collapse;
}
.table-modern thead th {
    background: #F8F9FA;
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    font-weight: 600;
    color: #4F4255;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 16px 20px;
    border-bottom: 2px solid #E0E0E0;
    border-top: none;
    white-space: nowrap;
}
.table-modern tbody td {
    padding: 16px 20px;
    vertical-align: middle;
    border-bottom: 1px solid #EEEEEE;
}
.table-modern tbody tr:last-child td {
    border-bottom: none;
}
.table-modern tbody tr:hover {
    background-color: #F8F9FA;
}

/* Mobile Responsive Data Tables */
@media (max-width: 768px) {
    .table-modern thead {
        display: none;
    }
    .table-modern tbody td {
        display: block;
        text-align: right !important;
        padding: 10px 15px;
        border-bottom: 1px solid #EEEEEE;
        position: relative;
    }
    .table-modern tbody td::before {
        content: attr(data-label);
        float: left;
        font-family: 'JetBrains Mono', monospace;
        font-size: 11px;
        font-weight: 600;
        color: #4F4255;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .table-modern tbody tr {
        border-bottom: 2px solid #E0E0E0;
        display: block;
        margin-bottom: 10px;
    }
    .table-modern tbody td:last-child {
        border-bottom: none;
    }
}
</style>

<?php include 'app/Views/templates/footer.php'; ?>
