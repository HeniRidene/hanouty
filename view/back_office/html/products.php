<?php
require_once '../../../controller/AuthController.php';
require_once '../../../model/Product.php';
$authController = new AuthController();
if (!$authController->isLoggedIn() || !$authController->isAdmin()) {
    header('Location: authentication-login.php');
    exit();
}
$productModel = new Product();
// Handle product deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product_id'])) {
    $productId = $_POST['delete_product_id'];
    $productModel->deleteProduct($productId);
    header('Location: products.php');
    exit();
}
$products = $productModel->getAllActiveProducts();
$currentUser = $authController->getCurrentUser();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Products - Hanouty Admin</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <style>
    .product-img-thumb { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; }
    .table td, .table th { vertical-align: middle; }
    .left-sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 220px; background: #fff; border-right: 1px solid #eee; z-index: 1040; }
    .nav.flex-column { margin-top: 2rem; }
    .nav.flex-column .nav-link { color: #333; font-weight: 500; padding: 0.75rem 1.5rem; border-radius: 0.5rem; margin-bottom: 0.5rem; transition: background 0.2s, color 0.2s; }
    .nav.flex-column .nav-link.active, .nav.flex-column .nav-link:hover { background: #f1f3f4; color: #198754; }
    .nav.flex-column .nav-link i { margin-right: 0.7rem; }
    .body-wrapper { margin-left: 220px; background: #f8f9fa; min-height: 100vh; }
    .app-header { background: #fff; border-bottom: 1px solid #eee; }
    .navbar .navbar-nav .nav-link img { border: 2px solid #dee2e6; }
    .dropdown-menu { min-width: 220px; }
    .btn-delete-product { margin-left: 8px; }
  </style>
</head>
<body>
  <!-- Sidebar Start -->
  <aside class="left-sidebar">
    <div>
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link" href="index.php">
            <i class="ti ti-home"></i> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="products.php">
            <i class="ti ti-package"></i> Products
          </a>
        </li>
      </ul>
    </div>
  </aside>
  <!-- Sidebar End -->
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
              <a class="nav-link dropdown-toggle" href="#" id="drop2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
    <!-- Header End -->
    <div class="container-fluid py-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title fw-semibold mb-4">Product List</h5>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Image</th>
                  <th>Title</th>
                  <th>Description</th>
                  <th>Supplier</th>
                  <th>Price</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($products as $product): ?>
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
                  <td><?= htmlspecialchars($product['title']) ?></td>
                  <td><?= htmlspecialchars($product['description']) ?></td>
                  <td><?= htmlspecialchars($product['supplier_name'] ?? 'Unknown') ?></td>
                  <td><?= number_format($product['price'], 2) ?> DT</td>
                  <td>
                    <a href="product-details.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-info">View Details</a>
                    <form method="POST" action="products.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                      <input type="hidden" name="delete_product_id" value="<?= $product['id'] ?>">
                      <button type="submit" class="btn btn-sm btn-outline-danger btn-delete-product" title="Delete Product">
                        <i class="ti ti-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 