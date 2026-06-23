<style>
    /* Enterprise Sidebar Styling */
    .main-sidebar {
        background-color: #F9F9F9 !important;
        border-right: 1px solid #E0E0E0 !important;
        box-shadow: none !important;
    }
    .brand-link {
        border-bottom: 1px solid #E0E0E0 !important;
        background-color: #F9F9F9 !important;
        text-align: center;
        padding: 1.25rem 0.5rem !important;
        display: block;
    }
    .brand-link img {
        max-width: 140px;
        margin-bottom: 0.5rem;
    }
    /* Remove text from brand-link if any, just keep image */
    .brand-text {
        display: none !important;
    }
    
    .nav-sidebar .nav-item > .nav-link {
        color: #4F4255 !important;
        font-family: 'JetBrains Mono', monospace !important;
        font-size: 12px !important;
        border-radius: 0 !important;
        margin: 0 !important;
        padding: 12px 16px !important;
        border-left: 3px solid transparent;
        transition: all 0.2s ease;
    }
    .nav-sidebar .nav-item > .nav-link:hover {
        background-color: #EEEEEE !important;
        color: #1A1C1C !important;
    }
    /* Active Link Styling */
    .nav-sidebar .nav-item > .nav-link.active {
        background-color: #fbebff !important; /* Very subtle purple */
        color: #A600FF !important;
        border-left: 3px solid #A600FF !important;
        font-weight: 600;
        box-shadow: none !important;
    }
    .nav-sidebar .nav-item > .nav-link.active i {
        color: #A600FF !important;
    }
    
    /* Submenu (Treeview) Styling */
    .nav-treeview {
        background-color: transparent !important;
        padding-left: 10px;
    }
    .nav-treeview > .nav-item > .nav-link {
        color: #4F4255 !important;
        font-family: 'Inter', sans-serif !important;
        font-size: 12px !important;
        padding: 8px 16px 8px 32px !important;
        border-left: 3px solid transparent !important;
        background: transparent !important;
    }
    .nav-treeview > .nav-item > .nav-link:hover {
        color: #1A1C1C !important;
        background-color: transparent !important;
    }
    .nav-treeview > .nav-item > .nav-link.active {
        color: #A600FF !important;
        font-weight: 600;
        background-color: transparent !important;
        box-shadow: none !important;
    }
    .nav-treeview > .nav-item > .nav-link.active i {
        color: #A600FF !important;
    }
    /* Icons */
    .nav-sidebar .nav-icon {
        font-size: 14px !important;
        margin-right: 12px !important;
        width: 20px;
        text-align: center;
        color: #807287;
    }
    .nav-sidebar .nav-link:hover .nav-icon {
        color: #1A1C1C;
    }
    .nav-sidebar .nav-link.active .nav-icon {
        color: #A600FF;
    }
</style>

<?php
$role = (int) session()->get('role');
$isSuperAdmin = $role === 5;
$isBranchAdmin = $role === 1;
$isAdminLike = in_array($role, [5, 1, 3, 4], true);
$isBranchManager = in_array($role, [5, 1], true);
$currentPath = trim((string) service('uri')->getPath(), '/');

$isCurrent = static function (array $paths) use ($currentPath): bool {
    foreach ($paths as $path) {
        $normalized = trim($path, '/');

        if ($normalized === $currentPath) {
            return true;
        }

        if ($normalized !== '' && str_starts_with($currentPath, $normalized . '/')) {
            return true;
        }
    }

    return false;
};

$adminDriverOpen = $isCurrent(['driverEntry', 'drivers']);
$adminVisitOpen = $isCurrent(['visitEntry', 'visitEntryList']);
$reportsOpen = $isCurrent(['reports']);
$financeOpen = $isCurrent(['expenses', 'payouts']);
$incentivesOpen = $isCurrent(['bonus-rules', 'driver-bonuses', 'incentive-rules']);
$whatsAppOpen = $isCurrent(['whatsapp-campaigns', 'message-templates']);
$usersOpen = $isCurrent(['user_list', 'create_user', 'edit_user', 'update_user']);
$branchesOpen = $isCurrent(['branches']);
$loyaltyOpen = $isCurrent(['loyalty']);
$vehicleOpsOpen = $isCurrent(['vehicle-branch-activity']);
?>

<aside class="main-sidebar">
    <a href="<?= base_url('dashboard') ?>" class="brand-link">
        <img src="<?= base_url('uploads/hawahhawai_logo1.png') ?>" alt="hawaa hawai logo" width="180">
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <?php if ($isAdminLike): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('dashboard') ?>" class="nav-link <?= $isCurrent(['dashboard']) ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-gauge"></i>
                            <p><?= lang('App.dashboard') ?></p>
                        </a>
                    </li>

                    <li class="nav-item <?= $adminDriverOpen ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $adminDriverOpen ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-id-card"></i>
                            <p>
                                <?= lang('App.drivers') ?>
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('driverEntry') ?>" class="nav-link <?= $isCurrent(['driverEntry', 'drivers']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p><?= lang('App.driver_list') ?></p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('drivers/create') ?>" class="nav-link <?= $isCurrent(['drivers/create']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p><?= lang('App.add_driver') ?></p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item <?= $adminVisitOpen ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $adminVisitOpen ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-door-open"></i>
                            <p>
                                <?= lang('App.visits') ?>
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('visitEntry') ?>" class="nav-link <?= $isCurrent(['visitEntry']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p><?= lang('App.visit_entry') ?></p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('visitEntryList') ?>" class="nav-link <?= $isCurrent(['visitEntryList']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p><?= lang('App.visit_list') ?></p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item <?= $incentivesOpen ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $incentivesOpen ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-percent"></i>
                            <p>
                                <?= lang('App.bonuses') ?>
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('bonus-rules') ?>" class="nav-link <?= $isCurrent(['bonus-rules', 'incentive-rules']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p><?= lang('App.bonus_rules') ?></p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('driver-bonuses') ?>" class="nav-link <?= $isCurrent(['driver-bonuses']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p><?= lang('App.driver_bonuses') ?></p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item <?= $whatsAppOpen ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $whatsAppOpen ? 'active' : '' ?>">
                            <i class="nav-icon fa-brands fa-whatsapp"></i>
                            <p>
                                <?= lang('App.whatsapp') ?>
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('whatsapp-campaigns') ?>" class="nav-link <?= $isCurrent(['whatsapp-campaigns']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p><?= lang('App.whatsapp_campaigns') ?></p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('message-templates') ?>" class="nav-link <?= $isCurrent(['message-templates']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p><?= lang('App.message_templates') ?></p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('reports') ?>" class="nav-link <?= $reportsOpen ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-file-lines"></i>
                            <p><?= lang('App.reports') ?></p>
                        </a>
                    </li>

                    <!-- <li class="nav-item <?= $financeOpen ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $financeOpen ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-wallet"></i>
                            <p>
                                Finance
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('expenses') ?>" class="nav-link <?= $isCurrent(['expenses']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Expenses</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('payouts') ?>" class="nav-link <?= $isCurrent(['payouts']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Payouts</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('vehicle-branch-activity') ?>" class="nav-link <?= $vehicleOpsOpen ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-car-side"></i>
                            <p>Vehicle Activity</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('loyalty') ?>" class="nav-link <?= $loyaltyOpen ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-medal"></i>
                            <p>Driver Loyalty</p>
                        </a>
                    </li> -->

                    <?php if ($isSuperAdmin): ?>
                        <li class="nav-item">
                            <a href="<?= base_url('branches') ?>" class="nav-link <?= $branchesOpen ? 'active' : '' ?>">
                                <i class="nav-icon fa-solid fa-store"></i>
                                <p><?= lang('App.branches') ?></p>
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item <?= $usersOpen ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $usersOpen ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-users"></i>
                            <p>
                                <?= lang('App.users') ?>
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('user_list') ?>" class="nav-link <?= $isCurrent(['user_list', 'edit_user']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p><?= lang('App.user_list') ?></p>
                                </a>
                            </li>
                            <?php if ($isBranchManager): ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('create_user') ?>" class="nav-link <?= $isCurrent(['create_user']) ? 'active' : '' ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p><?= lang('App.create_user') ?></p>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('profile') ?>" class="nav-link <?= $isCurrent(['profile']) ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-address-card"></i>
                            <p><?= lang('App.profile') ?></p>
                        </a>
                    </li>
                <?php elseif ($role === 2): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('reports') ?>" class="nav-link <?= $reportsOpen ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-file-lines"></i>
                            <p>Reports</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('expenses') ?>" class="nav-link <?= $isCurrent(['expenses']) ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-receipt"></i>
                            <p>Expenses</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('payouts') ?>" class="nav-link <?= $isCurrent(['payouts']) ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-wallet"></i>
                            <p>Payouts</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('loyalty') ?>" class="nav-link <?= $loyaltyOpen ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-medal"></i>
                            <p>Driver Loyalty</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('audit-trail') ?>" class="nav-link <?= $isCurrent(['audit-trail']) ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-shield-halved"></i>
                            <p>Audit Trail</p>
                        </a>
                    </li>
                <?php elseif (in_array($role, [3, 4], true)): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('visitEntry') ?>" class="nav-link <?= $isCurrent(['visitEntry']) ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-door-open"></i>
                            <p>Front Desk Entry</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('visitEntryList') ?>" class="nav-link <?= $isCurrent(['visitEntryList']) ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-list-check"></i>
                            <p>Visit List</p>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</aside>
