<?php
require_once '../../../controller/AuthController.php';
require_once '../../../controller/UserController.php';
require_once '../../../model/Product.php';
$authController = new AuthController();
$userController = new UserController();
$productModel = new Product();
if (!$authController->isLoggedIn() || !($authController->isAdmin() || $authController->isSupplier())) {
    header('Location: authentication-login.php');
    exit();
}
// Get statistics (replace with real queries as needed)
$users = $userController->getAllUsersWithDetails();
$userCount = count($users);
$supplierCount = count(array_filter($users, function($u){return $u['role']==='supplier';}));
$clientCount = count(array_filter($users, function($u){return $u['role']==='client';}));
$adminCount = count(array_filter($users, function($u){return $u['role']==='admin';}));
$roleCounts = [
    'Admin' => $adminCount,
    'Supplier' => $supplierCount,
    'Client' => $clientCount
];
$recentUsers = array_slice($users, 0, 5);

$products = $productModel->getAllActiveProducts();
$productCount = count($products);
$categoryCounts = [];
foreach ($products as $p) {
    $cat = $p['category'] ?: 'Uncategorized';
    if (!isset($categoryCounts[$cat])) $categoryCounts[$cat] = 0;
    $categoryCounts[$cat]++;
}
$recentProducts = array_slice($products, 0, 5);
$totalSales = 0; // Placeholder, replace with real sales data if available
$currentUser = $authController->getCurrentUser();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hanouty Admin Dashboard</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
<?php 
require_once '../components/Sidebar.php';
$sidebar = new Sidebar();
echo $sidebar->render();
?>
<div class="body-wrapper" style="margin-left:260px; padding: 20px;">
  <header class="app-header bg-white mb-4" style="margin: -20px -20px 20px -20px; padding: 0 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
    <nav class="navbar navbar-expand-lg navbar-light">
      <ul class="navbar-nav">
        <li class="nav-item d-block d-xl-none">
          <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>
      </ul>
      <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
          <li class="nav-item dropdown">
            <a class="nav-link " href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="../assets/images/profile/user-1.jpg" alt="" width="35" height="35" class="rounded-circle">
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
              <div class="message-body">
                <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                  <i class="ti ti-user fs-6"></i>
                  <p class="mb-0 fs-3"><?php echo htmlspecialchars($currentUser['name']); ?></p>
                </a>
                <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                  <i class="ti ti-mail fs-6"></i>
                  <p class="mb-0 fs-3"><?php echo htmlspecialchars($currentUser['email']); ?></p>
                </a>
                <a href="logout.php" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <div class="container-fluid">
    <!-- Welcome Banner -->
    <div class="card bg-primary text-white mb-4">
      <div class="card-body p-4">
        <div class="d-flex align-items-center">
          <div>
            <h3 class="fw-semibold mb-2">Welcome back, <?php echo htmlspecialchars($currentUser['name']); ?>!</h3>
            <p class="mb-0">Here's what's happening in your store today.</p>
          </div>
          <div class="ms-auto">
            <button class="btn btn-light">View Reports</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
      <div class="col-lg-6 mb-4">
        <div class="card h-100">
          <div class="card-header bg-transparent"><strong>User Roles Distribution</strong></div>
          <div class="card-body">
            <div id="userRolesChart" style="height:320px;"></div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 mb-4">
        <div class="card h-100">
          <div class="card-header bg-transparent"><strong>Products by Category</strong></div>
          <div class="card-body">
            <div id="productCategoriesChart" style="height:320px;"></div>
          </div>
        </div>
      </div>
    </div>
    <!-- Recent Users & Products Row -->
    <div class="row mb-4">
      <div class="col-lg-6 mb-4">
        <div class="card h-100">
          <div class="card-header bg-transparent"><strong>Recent Users</strong></div>
          <div class="card-body p-0">
            <ul class="list-group list-group-flush">
              <?php foreach ($recentUsers as $u): ?>
                <li class="list-group-item d-flex align-items-center">
                  <span class="badge bg-primary me-2" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-size:1.1rem;">
                    <i class="ti ti-user"></i>
                  </span>
                  <div>
                    <div class="fw-semibold"><?= htmlspecialchars($u['name']) ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($u['email']) ?> (<?= ucfirst($u['role']) ?>)</div>
                  </div>
                  <span class="ms-auto text-muted small"><?= date('Y-m-d', strtotime($u['created_at'])) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-lg-6 mb-4">
        <div class="card h-100">
          <div class="card-header bg-transparent"><strong>Recent Products</strong></div>
          <div class="card-body p-0">
            <ul class="list-group list-group-flush">
              <?php foreach ($recentProducts as $p): ?>
                <li class="list-group-item d-flex align-items-center">
                  <span class="badge bg-info me-2" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-size:1.1rem;">
                    <i class="ti ti-package"></i>
                  </span>
                  <div>
                    <div class="fw-semibold"><?= htmlspecialchars($p['title']) ?></div>
                    <div class="text-muted small">Category: <?= htmlspecialchars($p['category'] ?: 'Uncategorized') ?></div>
                  </div>
                  <span class="ms-auto text-muted small"><?= date('Y-m-d', strtotime($p['created_at'])) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Revenue & Welcome Cards -->
    <div class="row mt-4">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header bg-transparent">
            <div class="d-flex align-items-center">
              <h5 class="mb-0 fw-semibold">Revenue Overview</h5>
              <div class="ms-auto">
                <select class="form-select form-select-sm">
                  <option>Last 7 Days</option>
                  <option>Last Month</option>
                  <option>Last Year</option>
                </select>
              </div>
            </div>
          </div>
          <div class="card-body p-4">
            <div class="row align-items-center">
              <div class="col-8">
                <h4 class="fw-semibold mb-3">Total Revenue</h4>
                <h2 class="fw-semibold mb-3">$<?php echo number_format($totalSales, 2); ?></h2>
                <div class="d-flex align-items-center mb-3">
                  <span class="me-2 rounded-circle bg-success-subtle p-1 d-flex align-items-center justify-content-center">
                    <i class="ti ti-trending-up text-success fs-5"></i>
                  </span>
                  <p class="text-dark me-2 fs-3 mb-0">+9%</p>
                  <p class="fs-3 mb-0">last year</p>
                </div>
              </div>
              <div class="col-4">
                <div class="d-flex justify-content-center">
                  <div class="p-3 rounded-circle bg-primary-subtle">
                    <i class="ti ti-currency-dollar text-primary fs-8"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card bg-primary text-white">
          <div class="card-body p-4">
            <div class="d-flex align-items-center">
              <div>
                <h5 class="fw-semibold mb-4">Quick Actions</h5>
                <a href="user-management.php" class="btn btn-light mb-2 w-100">Manage Users</a>
                <a href="products.php" class="btn btn-light mb-2 w-100">View Products</a>
                <a href="featured-spots.php" class="btn btn-light w-100">Edit Featured Spots</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <style>
    .hover-shadow {
      transition: all 0.3s ease;
    }
    .hover-shadow:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .card {
      border: none;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    }
    .bg-primary-subtle {
      background-color: rgba(13,110,253,0.1);
    }
    .bg-success-subtle {
      background-color: rgba(25,135,84,0.1);
    }
    .bg-warning-subtle {
      background-color: rgba(255,193,7,0.1);
    }
    .bg-info-subtle {
      background-color: rgba(13,202,240,0.1);
    }
    </style>
  </div>
</div>
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="../assets/libs/apexcharts/src/assets/apexcharts.css" />
<script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // User Roles Donut Chart
    var userRolesOptions = {
      chart: { type: 'donut', height: 320 },
      series: <?= json_encode(array_values($roleCounts)) ?>,
      labels: <?= json_encode(array_keys($roleCounts)) ?>,
      colors: ['#6366f1', '#22c55e', '#f59e42'],
      legend: { position: 'bottom' },
      dataLabels: { enabled: true },
    };
    var userRolesChart = new ApexCharts(document.querySelector('#userRolesChart'), userRolesOptions);
    userRolesChart.render();
    // Product Categories Bar Chart
    var productCategoriesOptions = {
      chart: { type: 'bar', height: 320 },
      series: [{
        name: 'Products',
        data: <?= json_encode(array_values($categoryCounts)) ?>
      }],
      xaxis: {
        categories: <?= json_encode(array_keys($categoryCounts)) ?>,
        labels: { rotate: -45 }
      },
      colors: ['#0ea5e9'],
      dataLabels: { enabled: true },
    };
    var productCategoriesChart = new ApexCharts(document.querySelector('#productCategoriesChart'), productCategoriesOptions);
    productCategoriesChart.render();
  });
</script>
</body>
</html> 