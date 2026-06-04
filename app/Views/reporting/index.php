<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>

<?php
$activeType = $report['type'] ?? 'driver-report';
$filters = $report['filters'] ?? [];
$buildExportUrl = static function (string $format) use ($activeType, $filters): string {
    return base_url('reports/export/' . $activeType . '/' . $format . '?' . http_build_query($filters));
};

$reportIcons = [
    'driver-report' => 'fa-id-card',
    'visit-ledger' => 'fa-list-ul',
    'drivers-registered' => 'fa-sack-dollar',
    'monthly-bonuses' => 'fa-award',
];

$reportFilterMap = [
    'driver-report' => ['date' => true, 'search' => true, 'period' => false, 'limit' => true],
    'visit-ledger' => ['date' => true, 'search' => true, 'period' => false, 'limit' => true],
    'drivers-registered' => ['date' => true, 'search' => true, 'period' => false, 'limit' => true],
    'monthly-bonuses' => ['date' => false, 'search' => true, 'period' => true, 'limit' => false],
];

$rowCount = count($report['rows'] ?? []);
$currentYear = (int) date('Y');
?>

<?php
$pageTitle = 'Reports';
$pageSubtitle = (string) ($report['description'] ?? 'Pick a report, set filters, and run.');
$pageEyebrow = 'Analytics';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Reports', 'active' => true],
];
?>

<div
    class="content-wrapper ops-page-shell report-hub"
    data-active-type="<?= esc($activeType) ?>"
    data-filter-map="<?= esc(json_encode($reportFilterMap), 'attr') ?>"
    data-export-base="<?= esc(rtrim(base_url('reports/export'), '/'), 'attr') ?>"
    data-reports-base="<?= esc(base_url('reports'), 'attr') ?>"
    data-row-count="<?= (int) $rowCount ?>"
>
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <div class="card ops-card mb-3">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                    <h3 class="card-title mb-0">Choose report</h3>
                    <span class="report-active-hint text-muted" id="reportActiveHint">
                        <?= esc((string) ($reportDefinitions[$activeType]['label'] ?? 'Report')) ?>
                    </span>
                </div>
                <div class="card-body pb-2">
                    <div class="report-type-grid" role="tablist" aria-label="Report types">
                        <?php foreach ($reportDefinitions as $type => $definition): ?>
                            <?php
                            $isActive = $activeType === $type;
                            $icon = $reportIcons[$type] ?? 'fa-chart-line';
                            ?>
                            <button
                                type="button"
                                class="report-type-card<?= $isActive ? ' is-active' : '' ?>"
                                data-type="<?= esc($type) ?>"
                                data-label="<?= esc($definition['label']) ?>"
                                data-description="<?= esc($definition['description']) ?>"
                                role="tab"
                                aria-selected="<?= $isActive ? 'true' : 'false' ?>"
                            >
                                <span class="report-type-icon"><i class="fa-solid <?= esc($icon) ?>"></i></span>
                                <span class="report-type-label"><?= esc($definition['label']) ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <p class="report-type-description text-muted mb-0 mt-3 d-none" id="reportTypeDescription" aria-hidden="true">
                        <?= esc((string) ($report['description'] ?? '')) ?>
                    </p>
                </div>
            </div>

            <div class="card ops-card mb-3">
                <div class="card-header">
                    <h3 class="card-title mb-0">Filters</h3>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('reports') ?>" method="get" id="reportForm" class="report-form">
                        <input type="hidden" name="type" id="reportType" value="<?= esc($activeType) ?>">

                        <div class="report-filter-group<?= ($reportFilterMap[$activeType]['date'] ?? false) ? ' is-visible' : '' ?>" data-filter-group="date">
                            <label class="report-filter-heading">Date range</label>
                            <div class="report-quick-ranges btn-group btn-group-sm mb-2" role="group" aria-label="Quick date ranges">
                                <button type="button" class="btn btn-outline-secondary report-range-btn" data-range="today">Today</button>
                                <button type="button" class="btn btn-outline-secondary report-range-btn" data-range="week">This week</button>
                                <button type="button" class="btn btn-outline-secondary report-range-btn" data-range="month">This month</button>
                                <button type="button" class="btn btn-outline-secondary report-range-btn" data-range="last30">Last 30 days</button>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-sm-6 mb-3 mb-md-0">
                                    <label for="start_date">From</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?= esc((string) ($filters['start_date'] ?? '')) ?>">
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <label for="end_date">To</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?= esc((string) ($filters['end_date'] ?? '')) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="report-filter-group<?= ($reportFilterMap[$activeType]['period'] ?? false) ? ' is-visible' : '' ?>" data-filter-group="period">
                            <label class="report-filter-heading">Payout period</label>
                            <div class="row">
                                <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                                    <label for="year">Year</label>
                                    <select name="year" id="year" class="form-control">
                                        <?php for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
                                            <option value="<?= $y ?>" <?= (int) ($filters['year'] ?? $currentYear) === $y ? 'selected' : '' ?>><?= $y ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <label for="month">Month</label>
                                    <select name="month" id="month" class="form-control">
                                        <?php for ($m = 1; $m <= 12; $m++): ?>
                                            <option value="<?= $m ?>" <?= (int) ($filters['month'] ?? (int) date('m')) === $m ? 'selected' : '' ?>>
                                                <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="report-filter-group<?= ($reportFilterMap[$activeType]['search'] ?? false) ? ' is-visible' : '' ?>" data-filter-group="search">
                            <label class="report-filter-heading">Search</label>
                            <input
                                type="text"
                                name="search_input"
                                id="search_input"
                                class="form-control"
                                value="<?= esc((string) ($filters['search_input'] ?? '')) ?>"
                                placeholder="Driver name or mobile..."
                                autocomplete="off"
                            >
                        </div>

                        <div class="report-filter-group<?= ($reportFilterMap[$activeType]['limit'] ?? false) ? ' is-visible' : '' ?>" data-filter-group="limit">
                            <label class="report-filter-heading">How many rows</label>
                            <select name="limit" id="limit" class="form-control" style="max-width: 12rem;">
                                <?php foreach ([25, 50, 100, 200] as $limitOption): ?>
                                    <option value="<?= $limitOption ?>" <?= (int) ($filters['limit'] ?? 25) === $limitOption ? 'selected' : '' ?>>
                                        <?= $limitOption ?> rows
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="report-actions d-flex flex-wrap align-items-center">
                            <button type="submit" class="btn btn-primary mr-2" id="reportRunBtn">
                                <i class="fas fa-play mr-1"></i> Run report
                            </button>
                            <button type="button" class="btn btn-outline-secondary mr-2" id="reportResetBtn">
                                <i class="fas fa-rotate-left mr-1"></i> Reset
                            </button>
                            <div class="btn-group mr-2">
                                <button type="button" class="btn btn-outline-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="reportExportToggle"<?= $rowCount === 0 ? ' disabled' : '' ?>>
                                    <i class="fas fa-download mr-1"></i> Export
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item report-export-link" href="<?= esc($buildExportUrl('excel')) ?>" data-format="excel">Excel</a>
                                    <a class="dropdown-item report-export-link" href="<?= esc($buildExportUrl('pdf')) ?>" data-format="pdf" target="_blank" rel="noopener">PDF view</a>
                                </div>
                            </div>
                            <span class="report-result-badge badge badge-light" id="reportResultBadge">
                                <?= $rowCount ?> <?= $rowCount === 1 ? 'row' : 'rows' ?>
                            </span>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card ops-card report-results-card" id="reportResultsCard">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                    <h3 class="card-title mb-0" id="reportResultsTitle"><?= esc((string) ($report['title'] ?? 'Results')) ?></h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($report['summary'])): ?>
                        <div class="row dashboard-stat-grid mb-4" id="reportSummary">
                            <?php foreach ($report['summary'] as $label => $value): ?>
                                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                    <div class="profile-stat">
                                        <h4>
                                            <?php if (is_float($value) || str_contains((string) $value, '.')): ?>
                                                <?= esc(number_format((float) $value, 2)) ?>
                                            <?php else: ?>
                                                <?= esc((string) $value) ?>
                                            <?php endif; ?>
                                        </h4>
                                        <p><?= esc((string) $label) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="ops-table-wrap table-responsive">
                        <table class="table table-hover mb-0 <?= $rowCount > 0 ? 'data_table1' : '' ?>" id="reportDataTable">
                            <thead>
                                <tr>
                                    <?php foreach (($report['columns'] ?? []) as $column): ?>
                                        <th><?= esc((string) $column['label']) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (($report['rows'] ?? []) as $row): ?>
                                    <tr>
                                        <?php foreach (($report['columns'] ?? []) as $column): ?>
                                            <?php $value = $row[$column['key']] ?? ''; ?>
                                            <td>
                                                <?php if (($column['format'] ?? '') === 'currency'): ?>
                                                    <?= esc(number_format((float) $value, 2)) ?>
                                                <?php else: ?>
                                                    <?= esc((string) $value) ?>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($rowCount === 0): ?>
                        <div class="report-empty-state" id="reportEmptyState">
                            <i class="fa-solid fa-table-list"></i>
                            <p>No records for these filters.</p>
                            <p class="small text-muted mb-0">Try a wider date range or another report type.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'app/Views/templates/footer.php'; ?>
