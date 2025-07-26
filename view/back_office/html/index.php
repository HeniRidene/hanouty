<?php
require_once '../../../controller/AuthController.php';
require_once '../../../controller/UserController.php';
require_once '../../../model/Product.php';
$authController = new AuthController();
$userController = new UserController();
$productModel = new Product();
if (!$authController->isLoggedIn() || !$authController->isAdmin()) {
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
<aside class="left-sidebar">
  <div>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link active" href="index.php">
          <i class="ti ti-home"></i> Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="user-management.php">
          <i class="ti ti-users"></i> User Management
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="products.php">
          <i class="ti ti-package"></i> Products
        </a>
      </li>
    </ul>
  </div>
</aside>
<div class="body-wrapper" style="margin-left:220px;">
  <header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light">
      <ul class="navbar-nav">
        <li class="nav-item d-block d-xl-none">
          <a class="nav-link sidebartoggler " id="headerCollapse" href="javascript:void(0)">
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
  <div class="container-fluid mt-4">
    <div class="row">
      <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
          <div class="card-body text-center">
            <h5 class="card-title">Total Users</h5>
            <h2><?php echo $userCount; ?></h2>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
          <div class="card-body text-center">
            <h5 class="card-title">Suppliers</h5>
            <h2><?php echo $supplierCount; ?></h2>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
          <div class="card-body text-center">
            <h5 class="card-title">Clients</h5>
            <h2><?php echo $clientCount; ?></h2>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
          <div class="card-body text-center">
            <h5 class="card-title">Products</h5>
            <h2><?php echo $productCount; ?></h2>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-6 mb-4">
        <div class="card">
          <div class="card-body text-center">
            <h5 class="card-title">Total Sales</h5>
            <h2>$<?php echo number_format($totalSales, 2); ?></h2>
            <p class="text-muted">(Placeholder)</p>
          </div>
        </div>
      </div>
      <div class="col-lg-6 mb-4">
        <div class="card">
          <div class="card-body text-center">
            <h5 class="card-title">Welcome to Hanouty Admin Dashboard</h5>
            <p>Use the sidebar to manage users, products, and spot prices.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 