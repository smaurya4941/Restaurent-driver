<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>

<?php
$isEdit = isset($template) && $template;
$pageTitle = $isEdit ? lang('App.edit_template') : lang('App.create_template');
$pageSubtitle = lang('App.design_reusable_whatsapp_messages');
$pageEyebrow = lang('App.marketing');
$breadcrumbs = [
    ['label' => lang('App.home'), 'url' => base_url('dashboard')],
    ['label' => lang('App.templates'), 'url' => base_url('message-templates')],
    ['label' => $isEdit ? lang('App.edit') : lang('App.create'), 'active' => true],
];
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card ops-card mb-4">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;"><?= $isEdit ? lang('App.edit_message_template') : lang('App.new_message_template') ?></h3>
                            </div>
                        </div>
                        <form action="<?= base_url($isEdit ? 'message-templates/' . $template['id'] : 'message-templates') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name"><?= lang('App.template_name') ?></label>
                                    <input type="text" name="name" id="name" class="form-control" required maxlength="150" value="<?= esc(old('name', $template['name'] ?? '')) ?>" placeholder="<?= lang('App.example_template_name') ?>">
                                </div>

                                <div class="form-group">
                                    <label for="content"><?= lang('App.message_content') ?></label>
                                    <textarea name="content" id="content" rows="8" class="form-control" required maxlength="5000" placeholder="Type your message here..."><?= esc(old('content', $template['content'] ?? '')) ?></textarea>
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; margin-top: 8px;">
                                        <strong style="color: #1A1C1C;"><?= lang('App.available_variables') ?></strong><br>
                                        <span style="background: #F5F5F5; padding: 2px 4px; border-radius: 2px;">{{driver_name}}</span>, 
                                        <span style="background: #F5F5F5; padding: 2px 4px; border-radius: 2px;">{{visit_count}}</span>, 
                                        <span style="background: #F5F5F5; padding: 2px 4px; border-radius: 2px;">{{guest_count}}</span>, 
                                        <span style="background: #F5F5F5; padding: 2px 4px; border-radius: 2px;">{{city}}</span>, 
                                        <span style="background: #F5F5F5; padding: 2px 4px; border-radius: 2px;">{{vehicle_type}}</span>
                                    </div>
                                </div>

                                <div class="form-group mb-2 mt-4">
                                    <div class="custom-control custom-switch" style="padding-left: 2.25rem;">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" <?= old('is_active', $template['is_active'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="is_active" style="font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 500; color: #1A1C1C; text-transform: none; letter-spacing: 0;"><?= lang('App.template_is_active') ?></label>
                                    </div>
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #4F4255; margin-top: 6px;"><?= lang('App.inactive_template_warning') ?></div>
                                </div>
                            </div>
                            <div class="card-footer" style="background: #F8F9FA; border-top: 1px solid #E0E0E0; display: flex; gap: 10px;">
                                <button type="submit" class="btn btn-primary-enterprise"><?= $isEdit ? lang('App.update_template') : lang('App.save_template') ?></button>
                                <a href="<?= base_url('message-templates') ?>" class="btn btn-outline-enterprise"><?= lang('App.cancel') ?></a>
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
textarea.form-control {
    min-height: 80px;
}

/* Custom Switch for Enterprise */
.custom-control-input:checked ~ .custom-control-label::before {
    border-color: #A600FF;
    background-color: #A600FF;
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
