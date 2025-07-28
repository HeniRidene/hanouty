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
$productCount = count($productModel->getAllActiveProducts());
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

    <!-- Stats Row -->
    <div class="row">
      <div class="col-lg-3 col-md-6">
        <div class="card hover-shadow">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="rounded-circle bg-primary-subtle p-3 d-flex align-items-center justify-content-center">
                <i class="ti ti-users text-primary fs-5"></i>
              </div>
              <div class="ms-3">
                <h3 class="mb-0 fw-semibold"><?php echo $userCount; ?></h3>
                <p class="text-muted mb-0">Total Users</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="card hover-shadow">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="rounded-circle bg-success-subtle p-3 d-flex align-items-center justify-content-center">
                <i class="ti ti-building-store text-success fs-5"></i>
              </div>
              <div class="ms-3">
                <h3 class="mb-0 fw-semibold"><?php echo $supplierCount; ?></h3>
                <p class="text-muted mb-0">Suppliers</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="card hover-shadow">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="rounded-circle bg-warning-subtle p-3 d-flex align-items-center justify-content-center">
                <i class="ti ti-user-circle text-warning fs-5"></i>
              </div>
              <div class="ms-3">
                <h3 class="mb-0 fw-semibold"><?php echo $clientCount; ?></h3>
                <p class="text-muted mb-0">Clients</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="card hover-shadow">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="rounded-circle bg-info-subtle p-3 d-flex align-items-center justify-content-center">
                <i class="ti ti-package text-info fs-5"></i>
              </div>
              <div class="ms-3">
                <h3 class="mb-0 fw-semibold"><?php echo $productCount; ?></h3>
                <p class="text-muted mb-0">Products</p>
              </div>
            </div>
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
</body>
</html> 