<?php
require_once '../../../controller/AuthController.php';
require_once '../../../model/Product.php';
$authController = new AuthController();
if (!$authController->isLoggedIn() || !$authController->isAdmin()) {
    header('Location: authentication-login.php');
    exit();
}
$productModel = new Product();
$currentUser = $authController->getCurrentUser();

// Handle flash sale product deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product_id'])) {
    $productId = $_POST['delete_product_id'];
    $productModel->deleteProduct($productId);
    header('Location: flash-sales.php');
    exit();
}

// Get all flash sale products
try {
    $pdo = new PDO('mysql:host=localhost;dbname=hanouty', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $query = "SELECT p.*, u.name as supplier_name 
               FROM products p 
               LEFT JOIN users u ON p.user_id = u.id 
               WHERE p.is_flash_sale = 1 
               ORDER BY p.created_at DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $flashProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $flashProducts = [];
    $error = 'Database error: ' . $e->getMessage();
}

require_once '../components/Sidebar.php';
$sidebar = new Sidebar();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Flash Sales - Hanouty Admin</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <style>
    .product-img-thumb { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; }
    .table td, .table th { vertical-align: middle; }
    .body-wrapper { 
      margin-left: 260px; 
      background: #f8f9fa; 
      min-height: 100vh; 
      padding-top: 80px !important; /* Add top padding to avoid header overlap */
    }
    .app-header { 
      background: #fff; 
      border-bottom: 1px solid #eee; 
      position: fixed;
      top: 0;
      right: 0;
      left: 260px;
      z-index: 99;
      height: 70px;
    }
    .navbar .navbar-nav .nav-link img { border: 2px solid #dee2e6; }
    .dropdown-menu { min-width: 220px; }
    .btn-delete-product { margin-left: 8px; }
    /* Fix for table header visibility */
    .container-fluid { padding-top: 20px !important; }
    .card { margin-top: 20px; }
    .table-responsive { margin-top: 20px; }
    .table thead th { 
      padding-top: 15px; 
      padding-bottom: 15px;
      background-color: #f8f9fa;
      font-weight: 600;
      position: sticky;
      top: 0;
      z-index: 10;
    }
    .card-title { margin-bottom: 20px !important; }
  </style>
</head>
<body>
<?php echo $sidebar->render(); ?>
  <div class="body-wrapper">
    <!-- Header Start -->
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
              <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="drop2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="../assets/images/profile/user-1.jpg" alt="User Profile" width="35" height="35" class="rounded-circle">
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
                  <a href="logout.php" class="d-flex align-items-center gap-2 dropdown-item">
                    <i class="ti ti-power fs-6"></i>
                    <p class="mb-0 fs-3">Logout</p>
                  </a>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </nav>
    </header>
    <!-- Header End -->
    <div class="container-fluid py-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title fw-semibold mb-0">Flash Sales Management</h5>
            <span class="badge flash-sale-badge fs-6"><?php echo count($flashProducts); ?> Flash Sale Products</span>
          </div>
          
          <?php if (isset($error)): ?>
            <div class="alert alert-danger">
              <?php echo htmlspecialchars($error); ?>
            </div>
          <?php endif; ?>
          
          <?php if (empty($flashProducts)): ?>
            <div class="alert alert-info text-center">
              <i class="ti ti-flame fs-4 me-2"></i>
              No flash sale products found.
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Supplier</th>
                    <th>Price</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($flashProducts as $product): ?>
                  <tr>
                    <td>
                      <?php
                        $img = 'https://dummyimage.com/70x70/dee2e6/6c757d.jpg';
                        if (!empty($product['images'])) {
                          $imgs = @json_decode($product['images'], true);
                          if (is_array($imgs) && !empty($imgs[0])) {
                            $img = (strpos($imgs[0], '/') === 0 ? $imgs[0] : '/hanouty/' . ltrim($imgs[0], '/'));
                          }
                        }
                      ?>
                      <img src="<?= htmlspecialchars($img) ?>" class="product-img-thumb" alt="Product Image">
                    </td>
                    <td>
                      <div class="d-flex align-items-center">
                        <span class="fw-semibold"><?= htmlspecialchars($product['title']) ?></span>
                      </div>
                    </td>
                    <td>
                      <div class="text-muted" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                        <?= htmlspecialchars(substr($product['description'], 0, 100)) ?>
                        <?= strlen($product['description']) > 100 ? '...' : '' ?>
                      </div>
                    </td>
                    <td>
                      <span class="text-primary fw-medium">
                        <?= htmlspecialchars($product['supplier_name'] ?? 'Unknown') ?>
                      </span>
                    </td>
                    <td>
                      <span class="fw-bold text-success">
                        <?= number_format($product['price'], 2) ?> DT
                      </span>
                    </td>
                    <td>
                      <span class="text-muted">
                        <?= date('M j, Y', strtotime($product['created_at'])) ?>
                      </span>
                    </td>
                    <td>
                      <div class="d-flex gap-2">
                        <a href="product-details.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-info" title="View Details">
                          <i class="ti ti-eye"></i>
                        </a>
                        <a href="/hanouty/view/front_office/router.php?action=product&id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-primary" title="View in Front Office" target="_blank">
                          <i class="ti ti-external-link"></i>
                        </a>
                        <form method="POST" action="flash-sales.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this flash sale product? This action cannot be undone.');">
                          <input type="hidden" name="delete_product_id" value="<?= $product['id'] ?>">
                          <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Product">
                            <i class="ti ti-trash"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Flash Sales Statistics -->
      <div class="row mt-4">
        <div class="col-md-4">
          <div class="card bg-primary text-white">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="me-3">
                  <i class="ti ti-flame fs-1"></i>
                </div>
                <div>
                  <h6 class="card-title mb-0">Total Flash Sales</h6>
                  <h3 class="mb-0"><?= count($flashProducts) ?></h3>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-success text-white">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="me-3">
                  <i class="ti ti-users fs-1"></i>
                </div>
                <div>
                  <h6 class="card-title mb-0">Active Suppliers</h6>
                  <h3 class="mb-0"><?= count(array_unique(array_column($flashProducts, 'supplier_name'))) ?></h3>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-warning text-white">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="me-3">
                  <i class="ti ti-currency-dollar fs-1"></i>
                </div>
                <div>
                  <h6 class="card-title mb-0">Total Value</h6>
                  <h3 class="mb-0"><?= number_format(array_sum(array_column($flashProducts, 'price')), 2) ?> DT</h3>
                </div>
              </div>
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