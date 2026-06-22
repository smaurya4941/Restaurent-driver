<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>

<?php
$isEdit = isset($template) && $template;
$pageTitle = $isEdit ? 'Edit Template' : 'Create Template';
$pageSubtitle = 'Design reusable WhatsApp messages.';
$pageEyebrow = 'Marketing';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Templates', 'url' => base_url('message-templates')],
    ['label' => $isEdit ? 'Edit' : 'Create', 'active' => true],
];
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card ops-card">
                        <div class="card-header">
                            <h3 class="card-title"><?= $isEdit ? 'Edit Message Template' : 'New Message Template' ?></h3>
                        </div>
                        <form action="<?= base_url($isEdit ? 'message-templates/' . $template['id'] : 'message-templates') ?>" method="post">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Template Name</label>
                                    <input type="text" name="name" id="name" class="form-control" required maxlength="150" value="<?= esc(old('name', $template['name'] ?? '')) ?>" placeholder="e.g. Daily Offer, Welcome Message">
                                </div>

                                <div class="form-group">
                                    <label for="content">Message Content</label>
                                    <textarea name="content" id="content" rows="8" class="form-control" required maxlength="5000" placeholder="Type your message here..."><?= esc(old('content', $template['content'] ?? '')) ?></textarea>
                                    <small class="form-text text-muted">
                                        <strong>Available Variables:</strong><br>
                                        <code>{{driver_name}}</code>, <code>{{visit_count}}</code>, <code>{{guest_count}}</code>, <code>{{city}}</code>, <code>{{vehicle_type}}</code>
                                    </small>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" <?= old('is_active', $template['is_active'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="is_active">Template is Active</label>
                                    </div>
                                    <small class="form-text text-muted">Inactive templates will not appear in the WhatsApp Campaigns dropdown.</small>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update Template' : 'Save Template' ?></button>
                                <a href="<?= base_url('message-templates') ?>" class="btn btn-default">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/templates/footer.php'; ?>
