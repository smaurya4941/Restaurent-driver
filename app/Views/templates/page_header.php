<?php
$pageTitle = $pageTitle ?? 'Page';
$breadcrumbs = $breadcrumbs ?? [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => $pageTitle, 'active' => true],
];
?>
<section class="content-header app-page-header">
    <div class="container-fluid px-3 px-md-4">
        <div class="row align-items-center mb-0 mb-md-2">
            <div class="col-12 col-lg-7">
                <h1 class="m-0 app-page-title"><?= esc($pageTitle) ?></h1>
            </div>
            <div class="col-12 col-lg-5 d-none d-md-block">
                <ol class="breadcrumb float-sm-right mb-0">
                    <?php foreach ($breadcrumbs as $crumb): ?>
                        <?php if (!empty($crumb['active'])): ?>
                            <li class="breadcrumb-item active"><?= esc($crumb['label']) ?></li>
                        <?php else: ?>
                            <li class="breadcrumb-item">
                                <a href="<?= esc($crumb['url'] ?? '#') ?>"><?= esc($crumb['label']) ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </div>
</section>
