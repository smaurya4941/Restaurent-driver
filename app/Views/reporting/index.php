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

            <div class="row">
                <div class="col-lg-3">
                    <!-- Choose Report Card -->
                    <div class="card ops-card mb-4">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Reports</h3>
                            </div>
                        </div>
                        <div class="card-body p-3">
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
                                        <div class="report-type-info">
                                            <span class="report-type-label"><?= esc($definition['label']) ?></span>
                                        </div>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                            <p class="report-type-description text-muted mb-0 mt-3 d-none" id="reportTypeDescription" aria-hidden="true" style="font-family: 'JetBrains Mono', monospace; font-size: 11px;">
                                <?= esc((string) ($report['description'] ?? '')) ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <!-- Filters Card -->
                    <div class="card ops-card mb-4">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;" id="reportActiveHint"><?= esc((string) ($reportDefinitions[$activeType]['label'] ?? 'Report')) ?></h3>
                                <div class="text-muted" style="font-family: 'JetBrains Mono', monospace; font-size: 11px; text-transform: uppercase;">Filter & Extract Data</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('reports') ?>" method="get" id="reportForm" class="report-form">
                                <input type="hidden" name="type" id="reportType" value="<?= esc($activeType) ?>">

                                <div class="row">
                                    <!-- Date Filter -->
                                    <div class="col-md-6 report-filter-group<?= ($reportFilterMap[$activeType]['date'] ?? false) ? ' is-visible' : '' ?>" data-filter-group="date">
                                        <div class="form-group mb-3">
                                            <label class="report-filter-heading">Date Range</label>
                                            <div class="report-quick-ranges btn-group btn-group-sm mb-2" role="group" style="width: 100%;">
                                                <button type="button" class="btn btn-outline-enterprise report-range-btn py-1" data-range="today">Today</button>
                                                <button type="button" class="btn btn-outline-enterprise report-range-btn py-1" data-range="week">Week</button>
                                                <button type="button" class="btn btn-outline-enterprise report-range-btn py-1" data-range="month">Month</button>
                                                <button type="button" class="btn btn-outline-enterprise report-range-btn py-1" data-range="last30">30d</button>
                                            </div>
                                            <div class="d-flex" style="gap: 10px;">
                                                <div class="flex-fill">
                                                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?= esc((string) ($filters['start_date'] ?? '')) ?>">
                                                </div>
                                                <div class="flex-fill">
                                                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?= esc((string) ($filters['end_date'] ?? '')) ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Period Filter -->
                                    <div class="col-md-6 report-filter-group<?= ($reportFilterMap[$activeType]['period'] ?? false) ? ' is-visible' : '' ?>" data-filter-group="period">
                                        <div class="form-group mb-3">
                                            <label class="report-filter-heading">Payout Period</label>
                                            <div class="d-flex" style="gap: 10px;">
                                                <div class="flex-fill">
                                                    <select name="year" id="year" class="form-control">
                                                        <?php for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
                                                            <option value="<?= $y ?>" <?= (int) ($filters['year'] ?? $currentYear) === $y ? 'selected' : '' ?>><?= $y ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>
                                                <div class="flex-fill">
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
                                    </div>

                                    <!-- Search Filter -->
                                    <div class="col-md-3 report-filter-group<?= ($reportFilterMap[$activeType]['search'] ?? false) ? ' is-visible' : '' ?>" data-filter-group="search">
                                        <div class="form-group mb-3">
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
                                    </div>

                                    <!-- Limit Filter -->
                                    <div class="col-md-3 report-filter-group<?= ($reportFilterMap[$activeType]['limit'] ?? false) ? ' is-visible' : '' ?>" data-filter-group="limit">
                                        <div class="form-group mb-3">
                                            <label class="report-filter-heading">Rows</label>
                                            <select name="limit" id="limit" class="form-control">
                                                <?php foreach ([25, 50, 100, 200] as $limitOption): ?>
                                                    <option value="<?= $limitOption ?>" <?= (int) ($filters['limit'] ?? 25) === $limitOption ? 'selected' : '' ?>>
                                                        <?= $limitOption ?> rows
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="report-actions d-flex flex-wrap align-items-center mt-3 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary-enterprise mr-2" id="reportRunBtn">
                                        <i class="fas fa-play mr-1"></i> Run Report
                                    </button>
                                    <button type="button" class="btn btn-outline-enterprise mr-2" id="reportResetBtn">
                                        <i class="fas fa-rotate-left mr-1"></i> Reset
                                    </button>
                                    
                                    <div class="btn-group mr-auto">
                                        <button type="button" class="btn btn-outline-enterprise text-success border-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="reportExportToggle"<?= $rowCount === 0 ? ' disabled' : '' ?>>
                                            <i class="fas fa-file-export mr-1"></i> Export
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item report-export-link" href="<?= esc($buildExportUrl('excel')) ?>" data-format="excel"><i class="fas fa-file-excel mr-2 text-success"></i>Excel</a>
                                            <a class="dropdown-item report-export-link" href="<?= esc($buildExportUrl('csv')) ?>" data-format="csv"><i class="fas fa-file-csv mr-2 text-info"></i>CSV</a>
                                            <a class="dropdown-item report-export-link" href="<?= esc($buildExportUrl('pdf')) ?>" data-format="pdf" target="_blank" rel="noopener"><i class="fas fa-file-pdf mr-2 text-danger"></i>PDF</a>
                                        </div>
                                    </div>

                                    <span class="badge-enterprise-role bg-light text-dark border" id="reportResultBadge" style="font-size: 12px; padding: 6px 12px;">
                                        <?= $rowCount ?> <?= $rowCount === 1 ? 'row' : 'rows' ?> generated
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Results Card -->
                    <div class="card ops-card report-results-card" id="reportResultsCard">
                        <div class="card-body">
                            <?php if (!empty($report['summary'])): ?>
                                <div class="row mb-4" id="reportSummary">
                                    <?php foreach ($report['summary'] as $label => $value): ?>
                                        <div class="col-md-4 mb-3">
                                            <div class="p-3 border rounded shadow-sm" style="background: #F8F9FA;">
                                                <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase; margin-bottom: 4px;">
                                                    <?= esc((string) $label) ?>
                                                </div>
                                                <div style="font-family: 'Inter', sans-serif; font-size: 20px; font-weight: 700; color: #A600FF;">
                                                    <?php if (is_float($value) || str_contains((string) $value, '.')): ?>
                                                        <?= esc(number_format((float) $value, 2)) ?>
                                                    <?php else: ?>
                                                        <?= esc((string) $value) ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="ops-table-wrap table-responsive">
                                <table class="table table-modern mb-0 <?= $rowCount > 0 ? 'data_table1' : '' ?>" id="reportDataTable">
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
                                                    <td data-label="<?= esc((string) $column['label']) ?>">
                                                        <div style="font-family: 'Inter', sans-serif; font-size: 13px; color: #1A1C1C;">
                                                            <?php if (($column['format'] ?? '') === 'currency'): ?>
                                                                ₹<?= esc(number_format((float) $value, 2)) ?>
                                                            <?php else: ?>
                                                                <?= esc((string) $value) ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if ($rowCount === 0): ?>
                                <div class="text-center py-5" id="reportEmptyState">
                                    <div style="width: 60px; height: 60px; background: #F3E8FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                        <i class="fa-solid fa-chart-line text-primary" style="font-size: 24px; color: #A600FF !important;"></i>
                                    </div>
                                    <h5 style="font-family: 'Hanken Grotesk', sans-serif; font-weight: 600; color: #1A1C1C;">No records found</h5>
                                    <p class="text-muted mb-0" style="font-size: 13px;">Try adjusting your filters or date range.</p>
                                </div>
                            <?php endif; ?>
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
   FORM INPUTS & LABELS
========================================= */
.report-filter-heading, .form-group label {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    font-weight: 500;
    color: #4F4255;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 6px;
    display: block;
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
.report-filter-group {
    display: none;
}
.report-filter-group.is-visible {
    display: block;
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
    padding: 8px 16px;
    transition: all 0.2s;
    text-decoration: none;
}
.btn-outline-enterprise:hover {
    background: #F5F5F5;
    border-color: #1A1C1C;
}

/* =========================================
   SIDEBAR REPORT TABS
========================================= */
.report-type-grid {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.report-type-card {
    display: flex;
    align-items: center;
    background: transparent;
    border: 1px solid transparent;
    border-radius: 4px;
    padding: 12px;
    cursor: pointer;
    text-align: left;
    transition: all 0.2s;
    width: 100%;
}
.report-type-card:hover {
    background: #F8F9FA;
    border-color: #E0E0E0;
}
.report-type-card.is-active {
    background: #F3E8FF;
    border-color: #A600FF;
}
.report-type-card .report-type-icon {
    font-size: 18px;
    color: #4F4255;
    margin-right: 12px;
    width: 24px;
    text-align: center;
}
.report-type-card.is-active .report-type-icon {
    color: #A600FF;
}
.report-type-card .report-type-info {
    display: flex;
    flex-direction: column;
}
.report-type-card .report-type-label {
    font-family: 'Hanken Grotesk', sans-serif;
    font-weight: 600;
    font-size: 14px;
    color: #1A1C1C;
}
.report-type-card.is-active .report-type-label {
    color: #A600FF;
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

@media (max-width: 768px) {
    .table-modern thead { display: none; }
    .table-modern tbody td {
        display: block;
        text-align: right !important;
        padding: 10px 15px;
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
    }
    .table-modern tbody tr {
        border-bottom: 2px solid #E0E0E0;
        display: block;
        margin-bottom: 10px;
    }
}
</style>

<?php include 'app/Views/templates/footer.php'; ?>
