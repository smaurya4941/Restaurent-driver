<style>
    .dropdown-menu .dropdown-item:hover {
        background-color: #343a40;
        color: #fff;
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
$whatsAppOpen = $isCurrent(['whatsapp-campaigns']);
$usersOpen = $isCurrent(['user_list', 'create_user', 'edit_user', 'update_user']);
$branchesOpen = $isCurrent(['branches']);
$loyaltyOpen = $isCurrent(['loyalty']);
$vehicleOpsOpen = $isCurrent(['vehicle-branch-activity']);
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
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
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-item <?= $adminDriverOpen ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $adminDriverOpen ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-id-card"></i>
                            <p>
                                Drivers
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('driverEntry') ?>" class="nav-link <?= $isCurrent(['driverEntry', 'drivers']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Driver List</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('drivers/create') ?>" class="nav-link <?= $isCurrent(['drivers/create']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Add Driver</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item <?= $adminVisitOpen ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $adminVisitOpen ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-door-open"></i>
                            <p>
                                Visits
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('visitEntry') ?>" class="nav-link <?= $isCurrent(['visitEntry']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Visit Entry</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('visitEntryList') ?>" class="nav-link <?= $isCurrent(['visitEntryList']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Visit List</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item <?= $incentivesOpen ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $incentivesOpen ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-percent"></i>
                            <p>
                                Bonuses
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('bonus-rules') ?>" class="nav-link <?= $isCurrent(['bonus-rules', 'incentive-rules']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Bonus Rules</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('driver-bonuses') ?>" class="nav-link <?= $isCurrent(['driver-bonuses']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Driver Bonuses</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item <?= $whatsAppOpen ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $whatsAppOpen ? 'active' : '' ?>">
                            <i class="nav-icon fa-brands fa-whatsapp"></i>
                            <p>
                                WhatsApp
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('whatsapp-campaigns') ?>" class="nav-link <?= $isCurrent(['whatsapp-campaigns']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Driver Report</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('reports') ?>" class="nav-link <?= $reportsOpen ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-file-lines"></i>
                            <p>Reports</p>
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
                                <p>Branches</p>
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item <?= $usersOpen ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $usersOpen ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-users"></i>
                            <p>
                                Users
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('user_list') ?>" class="nav-link <?= $isCurrent(['user_list', 'edit_user']) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>User List</p>
                                </a>
                            </li>
                            <?php if ($isBranchManager): ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('create_user') ?>" class="nav-link <?= $isCurrent(['create_user']) ? 'active' : '' ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Create User</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('profile') ?>" class="nav-link <?= $isCurrent(['profile']) ? 'active' : '' ?>">
                            <i class="nav-icon fa-solid fa-address-card"></i>
                            <p>Profile</p>
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
