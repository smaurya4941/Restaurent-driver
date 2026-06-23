<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>

<?php
$pageTitle = 'WhatsApp Campaigns';
$pageSubtitle = 'Send special offers to opted-in drivers grouped by visit activity.';
$pageEyebrow = 'Marketing';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'WhatsApp', 'active' => true],
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
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Group Drivers</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('whatsapp-campaigns') ?>" method="get">
                                <div class="form-group">
                                    <label for="vehicle_type">Vehicle Type</label>
                                    <?php $selectedVehicleType = $currentFilters['vehicle_type'] ?? ''; ?>
                                    <select name="vehicle_type" id="vehicle_type" class="form-control">
                                        <option value="">All</option>
                                        <option value="bus" <?= $selectedVehicleType === 'bus' ? 'selected' : '' ?>>Bus</option>
                                        <option value="cab" <?= $selectedVehicleType === 'cab' ? 'selected' : '' ?>>Cab</option>
                                        <option value="traveller" <?= $selectedVehicleType === 'traveller' ? 'selected' : '' ?>>Traveller</option>
                                        <option value="truck" <?= $selectedVehicleType === 'truck' ? 'selected' : '' ?>>Truck</option>
                                        <option value="tempo" <?= $selectedVehicleType === 'tempo' ? 'selected' : '' ?>>Tempo</option>
                                        <option value="private taxi" <?= $selectedVehicleType === 'private taxi' ? 'selected' : '' ?>>Private Taxi</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="min_visits">Minimum Visits</label>
                                    <input type="number" name="min_visits" id="min_visits" class="form-control" min="0" value="<?= esc($currentFilters['min_visits'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label for="max_visits">Maximum Visits</label>
                                    <input type="number" name="max_visits" id="max_visits" class="form-control" min="0" value="<?= esc($currentFilters['max_visits'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label for="min_guests">Minimum Guest Count</label>
                                    <input type="number" name="min_guests" id="min_guests" class="form-control" min="0" value="<?= esc($currentFilters['min_guests'] ?? '') ?>">
                                </div>
                                <div class="form-group mb-4">
                                    <label for="max_guests">Maximum Guest Count</label>
                                    <input type="number" name="max_guests" id="max_guests" class="form-control" min="0" value="<?= esc($currentFilters['max_guests'] ?? '') ?>">
                                </div>

                                <button type="submit" class="btn btn-outline-enterprise w-100"><i class="fas fa-search mr-1"></i> View Group</button>
                            </form>
                        </div>
                    </div>

                    <div class="card ops-card mb-4">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Send Message</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('whatsapp-campaigns/send') ?>" method="post" enctype="multipart/form-data" id="whatsapp-send-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="vehicle_type" value="<?= esc($currentFilters['vehicle_type'] ?? '') ?>">
                                <input type="hidden" name="min_visits" value="<?= esc($currentFilters['min_visits'] ?? '') ?>">
                                <input type="hidden" name="max_visits" value="<?= esc($currentFilters['max_visits'] ?? '') ?>">
                                <input type="hidden" name="min_guests" value="<?= esc($currentFilters['min_guests'] ?? '') ?>">
                                <input type="hidden" name="max_guests" value="<?= esc($currentFilters['max_guests'] ?? '') ?>">

                                <div class="form-group">
                                    <label for="template_select">Select Template</label>
                                    <select id="template_select" class="form-control">
                                        <option value="">-- Custom Message (No Template) --</option>
                                        <?php foreach (($templates ?? []) as $template): ?>
                                            <option value="<?= esc($template['content']) ?>"><?= esc($template['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="message_body">WhatsApp Message</label>
                                    <textarea name="message_body" id="message_body" rows="6" class="form-control" placeholder="Example: Hello {{driver_name}}, you completed {{visit_count}} visits and brought {{guest_count}} guests."><?= esc(old('message_body')) ?></textarea>
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #4F4255; margin-top: 6px;">Supported variables: {{driver_name}}, {{visit_count}}, {{guest_count}}, {{city}}, {{vehicle_type}}</div>
                                </div>

                                <div class="form-group">
                                    <label for="media_url">Image URL</label>
                                    <input type="url" name="media_url" id="media_url" class="form-control" maxlength="2048" value="<?= esc(old('media_url')) ?>" placeholder="https://example.com/image.jpg">
                                </div>

                                <div class="form-group mb-4">
                                    <label for="message_image">Select Image</label>
                                    <input type="file" name="message_image" id="message_image" class="form-control" accept="image/jpeg,image/png,image/webp" style="padding-top: 7px; padding-bottom: 7px;">
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #4F4255; margin-top: 6px;">Optional. Max 5 MB. JPG, PNG, and WebP are allowed.</div>
                                </div>

                                <div class="d-flex flex-column" style="gap: 10px;">
                                    <button type="submit" name="send_scope" value="selected" class="btn btn-primary-enterprise w-100" <?= empty($groupRows) ? 'disabled' : '' ?>>
                                        <i class="fab fa-whatsapp mr-1"></i> Send To Selected
                                    </button>
                                    <button type="submit" name="send_scope" value="group" class="btn btn-outline-enterprise w-100" <?= empty($groupRows) ? 'disabled' : '' ?> onclick="return confirm('Send this message to every driver in the current group?');">
                                        <i class="fas fa-users mr-1"></i> Send To Group
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card ops-card">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Grouped Driver Report</h3>
                            </div>
                            <span class="badge-enterprise-role" style="background: #E0E0E0; color: #1A1C1C;">Matching drivers: <?= esc((string) count($groupRows ?? [])) ?></span>
                        </div>
                        <div class="card-body ops-table-wrap p-0">
                            <div class="table-responsive">
                                <table class="table table-modern mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px; text-align: center;">
                                                <input type="checkbox" id="select_all_drivers" aria-label="Select all drivers">
                                            </th>
                                            <th>Driver</th>
                                            <th>WhatsApp</th>
                                            <th>Status</th>
                                            <th>City</th>
                                            <th>Vehicle</th>
                                            <th>Visits</th>
                                            <th>Guests</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (($groupRows ?? []) as $row): ?>
                                            <tr>
                                                <td data-label="Select" style="text-align: center;">
                                                    <input
                                                        type="checkbox"
                                                        name="selected_driver_ids[]"
                                                        value="<?= esc((string) $row['id']) ?>"
                                                        form="whatsapp-send-form"
                                                        class="driver-select-checkbox"
                                                        aria-label="Select <?= esc($row['full_name']) ?>"
                                                    >
                                                </td>
                                                <td data-label="Driver">
                                                    <div style="font-weight: 600; color: #1A1C1C;">
                                                        <?= esc($row['full_name']) ?>
                                                    </div>
                                                </td>
                                                <td data-label="WhatsApp">
                                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #1A1C1C;">
                                                        <?= esc($row['whatsapp_number']) ?>
                                                    </div>
                                                </td>
                                                <td data-label="Status">
                                                    <?php $status = esc($row['status']); ?>
                                                    <span class="badge-enterprise-role" style="background: <?= in_array($status, ['blocked', 'blacklisted']) ? '#F43F5E' : '#10B981' ?>; color: #FFFFFF;">
                                                        <?= ucfirst($status) ?>
                                                    </span>
                                                </td>
                                                <td data-label="City">
                                                    <div style="font-family: 'Inter', sans-serif; font-size: 13px; color: #4F4255;">
                                                        <?= esc($row['city'] ?? '-') ?>
                                                    </div>
                                                </td>
                                                <td data-label="Vehicle">
                                                    <div style="font-family: 'Inter', sans-serif; font-size: 13px; color: #4F4255;">
                                                        <?= esc($row['vehicle_type'] ?? '-') ?>
                                                    </div>
                                                </td>
                                                <td data-label="Visits">
                                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 13px; font-weight: 600; color: #1A1C1C;">
                                                        <?= esc((string) ((int) ($row['visit_count'] ?? 0))) ?>
                                                    </div>
                                                </td>
                                                <td data-label="Guests">
                                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 13px; font-weight: 600; color: #1A1C1C;">
                                                        <?= esc((string) ((int) ($row['total_guests'] ?? 0))) ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($groupRows)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4" style="font-family: 'Inter', sans-serif; font-size: 14px;">
                                                    No drivers matched the current report filters.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer" style="background: #F8F9FA; border-top: 1px solid #E0E0E0; font-family: 'Inter', sans-serif; font-size: 13px; color: #4F4255;">
                            <span id="selected_driver_count" style="font-weight: 600; color: #A600FF;">Selected: 0</span>
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
.btn-primary-enterprise:disabled, .btn-outline-enterprise:disabled {
    opacity: 0.6;
    cursor: not-allowed;
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

/* DataTables Global Overrides for Enterprise View */
div.dataTables_wrapper div.dataTables_length label {
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    color: #4F4255;
    margin-left: 20px;
    margin-top: 15px;
}
div.dataTables_wrapper div.dataTables_length select {
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    padding: 4px 8px;
    margin: 0 4px;
}
div.dataTables_wrapper div.dataTables_filter label {
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    color: #4F4255;
    margin-right: 20px;
    margin-top: 15px;
}
div.dataTables_wrapper div.dataTables_filter input {
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    padding: 6px 10px;
    margin-left: 8px;
}
div.dataTables_wrapper div.dataTables_filter input:focus {
    border-color: #A600FF;
    outline: none;
}
div.dataTables_wrapper div.dataTables_info {
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    color: #4F4255;
    padding: 20px;
}
div.dataTables_wrapper div.dataTables_paginate {
    padding: 20px;
}
.page-item.active .page-link {
    background-color: #A600FF;
    border-color: #A600FF;
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select_all_drivers');
    const selectedCount = document.getElementById('selected_driver_count');
    const checkboxes = Array.from(document.querySelectorAll('.driver-select-checkbox'));

    const updateSelectedCount = function () {
        const checkedCount = checkboxes.filter(function (checkbox) {
            return checkbox.checked;
        }).length;

        if (selectedCount) {
            selectedCount.textContent = 'Selected: ' + checkedCount;
        }

        if (selectAll) {
            selectAll.checked = checkboxes.length > 0 && checkedCount === checkboxes.length;
            selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
        }
    };

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = selectAll.checked;
            });
            updateSelectedCount();
        });
    }

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    const templateSelect = document.getElementById('template_select');
    const messageBody = document.getElementById('message_body');

    if (templateSelect && messageBody) {
        templateSelect.addEventListener('change', function () {
            if (this.value !== '') {
                messageBody.value = this.value;
            }
        });
    }

    updateSelectedCount();
});
</script>

<?php include 'app/Views/templates/footer.php'; ?>
