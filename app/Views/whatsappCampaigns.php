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
                    <div class="card ops-card">
                        <div class="card-header">
                            <h3 class="card-title">Group Drivers</h3>
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
                                <div class="form-group">
                                    <label for="max_guests">Maximum Guest Count</label>
                                    <input type="number" name="max_guests" id="max_guests" class="form-control" min="0" value="<?= esc($currentFilters['max_guests'] ?? '') ?>">
                                </div>

                                <button type="submit" class="btn btn-success">View Group</button>
                            </form>
                        </div>
                    </div>

                    <div class="card ops-card">
                        <div class="card-header">
                            <h3 class="card-title">Send Message</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('whatsapp-campaigns/send') ?>" method="post" enctype="multipart/form-data" id="whatsapp-send-form">
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
                                    <small class="form-text text-muted">Supported variables: {{driver_name}}, {{visit_count}}, {{guest_count}}, {{city}}, {{vehicle_type}}</small>
                                </div>

                                <div class="form-group">
                                    <label for="media_url">Image URL</label>
                                    <input type="url" name="media_url" id="media_url" class="form-control" maxlength="2048" value="<?= esc(old('media_url')) ?>" placeholder="https://example.com/image.jpg">
                                    <!-- <small class="form-text text-muted">Optional fallback. If you select an image below, the uploaded image will be used.</small> -->
                                </div>

                                <div class="form-group">
                                    <label for="message_image">Select Image</label>
                                    <input type="file" name="message_image" id="message_image" class="form-control-file" accept="image/jpeg,image/png,image/webp">
                                    <small class="form-text text-muted">Optional. Max 5 MB. JPG, PNG, and WebP are allowed.</small>
                                </div>

                                <div class="btn-group d-flex" role="group">
                                    <button type="submit" name="send_scope" value="selected" class="btn btn-primary" <?= empty($groupRows) ? 'disabled' : '' ?>>
                                        Send To Selected
                                    </button>
                                    <button type="submit" name="send_scope" value="group" class="btn btn-outline-primary" <?= empty($groupRows) ? 'disabled' : '' ?> onclick="return confirm('Send this message to every driver in the current group?');">
                                        Send To Group
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card ops-card">
                        <div class="card-header">
                            <h3 class="card-title">Grouped Driver Report</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-bordered table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>
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
                                            <td>
                                                <input
                                                    type="checkbox"
                                                    name="selected_driver_ids[]"
                                                    value="<?= esc((string) $row['id']) ?>"
                                                    form="whatsapp-send-form"
                                                    class="driver-select-checkbox"
                                                    aria-label="Select <?= esc($row['full_name']) ?>"
                                                >
                                            </td>
                                            <td><?= esc($row['full_name']) ?></td>
                                            <td><?= esc($row['whatsapp_number']) ?></td>
                                            <td><?= esc($row['status']) ?></td>
                                            <td><?= esc($row['city'] ?? '-') ?></td>
                                            <td><?= esc($row['vehicle_type'] ?? '-') ?></td>
                                            <td><?= esc((string) ((int) ($row['visit_count'] ?? 0))) ?></td>
                                            <td><?= esc((string) ((int) ($row['total_guests'] ?? 0))) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($groupRows)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No drivers matched the current report filters.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            Matching drivers: <strong><?= esc((string) count($groupRows ?? [])) ?></strong>
                            <span class="ml-3 text-muted" id="selected_driver_count">Selected: 0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

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
