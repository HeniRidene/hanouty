<?php
class SupplierSidebar {
    private $currentPage;

    public function __construct() {
        $this->currentPage = basename($_SERVER['PHP_SELF']);
    }

    public function render() {
        ob_start();
        ?>
        <!-- Supplier Sidebar -->
        <aside class="left-sidebar">
            <div>
                <div class="brand-logo d-flex align-items-center justify-content-between">
                    <a href="supplier-dashboard.php" class="text-nowrap logo-img">
                        <img src="../assets/images/logos/logo.svg" width="180" alt="" />
                    </a>
                    <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                        <i class="ti ti-x"></i>
                    </div>
                </div>
                <nav class="sidebar-nav scroll-sidebar" data-simplebar>
                    <ul id="sidebarnav">
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Supplier Menu</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="supplier-dashboard.php" aria-expanded="false">
                                <span>
                                    <i class="ti ti-layout-dashboard"></i>
                                </span>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="supplier-profile.php" aria-expanded="false">
                                <span>
                                    <i class="ti ti-user"></i>
                                </span>
                                <span class="hide-menu">My Profile</span>
                            </a>
                        </li>
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Navigation</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="buy_spot.php" aria-expanded="false">
                                <span>
                                    <i class="ti ti-star"></i>
                                </span>
                                <span class="hide-menu">Buy Spot</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="/hanouty/view/front_office/router.php" aria-expanded="false">
                                <span>
                                    <i class="ti ti-world"></i>
                                </span>
                                <span class="hide-menu">Go to Front Office</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="authentication-login.php?logout=1" aria-expanded="false">
                                <span>
                                    <i class="ti ti-logout"></i>
                                </span>
                                <span class="hide-menu">Logout</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <?php
        return ob_get_clean();
    }
}