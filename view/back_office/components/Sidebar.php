<?php
/**
 * Sidebar Component for Back Office
 * Part of the View layer in MVC architecture
 */

class Sidebar {
    private $currentPage;

    public function __construct() {
        $this->currentPage = basename($_SERVER['PHP_SELF']);
    }

    public function render() {
        ob_start();
        ?>
        <aside class="left-sidebar">
            <div class="brand-logo d-flex align-items-center justify-content-between p-4">
                <a href="index.php" class="text-nowrap logo-img">
                    <h4 class="mb-0 fw-bold">Hanouty Admin</h4>
                </a>
                <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                    <i class="ti ti-x fs-8"></i>
                </div>
            </div>
            <nav class="sidebar-nav scroll-sidebar" data-simplebar>
                <ul id="sidebarnav" class="sidebar-nav">
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">MENU</span>
                    </li>
                    <?php echo $this->renderMenuItem('index.php', 'ti-layout-dashboard', 'Dashboard'); ?>
                    <?php echo $this->renderMenuItem('user-management.php', 'ti-users', 'User Management'); ?>
                    <?php echo $this->renderMenuItem('products.php', 'ti-package', 'Products'); ?>

                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">FEATURES</span>
                    </li>
                    <?php echo $this->renderMenuItem('featured-spots.php', 'ti-star', 'Featured Spots'); ?>
                    <?php echo $this->renderMenuItem('flash-sales.php', 'ti-flame', 'Flash Sales'); ?>

                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">EXTRA</span>
                    </li>
                    <?php echo $this->renderMenuItem('hanouty/view/front_office/router.php', 'ti-shopping-cart', 'View Shop', true); ?>
                </ul>
            </nav>
        </aside>

        <style>
        .left-sidebar {
            width: 260px;
            background: #fff;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 100;
        }

        .sidebar-nav {
            padding: 15px;
        }

        .nav-small-cap {
            padding: 15px 15px 10px;
            font-size: 12px;
            font-weight: 600;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sidebar-item {
            margin-bottom: 5px;
        }

        .sidebar-link {
            padding: 12px 15px;
            display: flex;
            align-items: center;
            color: #2a3547;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            border-radius: 7px;
            white-space: nowrap;
            transition: 0.3s ease-in-out;
        }

        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .sidebar-link i {
            font-size: 18px;
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .brand-logo {
            border-bottom: 1px solid #e9ecef;
        }

        .brand-logo h4 {
            color: #2a3547;
        }

        @media (max-width: 1199px) {
            .left-sidebar {
                transform: translateX(-100%);
                transition: 0.3s ease-in-out;
            }
            .left-sidebar.show {
                transform: translateX(0);
            }
        }
        </style>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('sidebarCollapse')?.addEventListener('click', function() {
                document.querySelector('.left-sidebar').classList.remove('show');
            });

            document.getElementById('headerCollapse')?.addEventListener('click', function() {
                document.querySelector('.left-sidebar').classList.add('show');
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }

    private function renderMenuItem($page, $icon, $label, $newTab = false) {
        $isActive = $this->currentPage === $page ? 'active' : '';
        $target = $newTab ? 'target="_blank"' : '';
        return "
        <li class='sidebar-item'>
            <a class='sidebar-link {$isActive}' href='{$page}' {$target} aria-expanded='false'>
                <span><i class='ti {$icon}'></i></span>
                <span class='hide-menu'>{$label}</span>
            </a>
        </li>";
    }
}
?>
