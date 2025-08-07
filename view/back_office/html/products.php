<?php
require_once '../../../controller/AuthController.php';
require_once '../../../model/Product.php';

$authController = new AuthController();
if (!$authController->isLoggedIn() || !$authController->isAdmin()) {
    header('Location: authentication-login.php');
    exit();
}

$productModel = new Product();

// Handle global max images setting update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_global_max_images'])) {
    $newGlobalMax = intval($_POST['global_max_images'] ?? 5);
    
    // Store in session
    $_SESSION['global_max_images'] = $newGlobalMax;
    
    // Also store in a config file or database for persistence
    $configFile = $_SERVER['DOCUMENT_ROOT'] . '/hanouty/config/global_settings.json';
    $configDir = dirname($configFile);
    
    // Create config directory if it doesn't exist
    if (!is_dir($configDir)) {
        mkdir($configDir, 0755, true);
    }
    
    // Read existing config or create new
    $config = [];
    if (file_exists($configFile)) {
        $config = json_decode(file_get_contents($configFile), true) ?: [];
    }
    
    // Update global max images setting
    $config['global_max_images'] = $newGlobalMax;
    
    // Save to file
    file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
    
    $globalUpdateMessage = 'Default max images updated to ' . $newGlobalMax . ' successfully!';
    error_log("Global max images updated to: " . $newGlobalMax);
}

// Handle max images update for specific product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_max_images_id'])) {
    $productId = intval($_POST['update_max_images_id']);
    $maxImages = isset($_POST['max_product_images']) ? intval($_POST['max_product_images']) : null;
    if ($maxImages !== null && $maxImages > 0) {
        $productModel->updateProduct($productId, ['max_product_images' => $maxImages]);
        $productUpdateMessage = 'Product max images updated successfully!';
    }
    header('Location: products.php');
    exit();
}

// Handle product deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product_id'])) {
    $productId = $_POST['delete_product_id'];
    $productModel->deleteProduct($productId);
    header('Location: products.php');
    exit();
}

// Load global max images setting from persistent storage
$globalMaxImages = 5; // Default fallback
$configFile = $_SERVER['DOCUMENT_ROOT'] . '/hanouty/config/global_settings.json';

if (file_exists($configFile)) {
    $config = json_decode(file_get_contents($configFile), true);
    if (isset($config['global_max_images']) && intval($config['global_max_images']) > 0) {
        $globalMaxImages = intval($config['global_max_images']);
        // Also update session to keep it in sync
        $_SESSION['global_max_images'] = $globalMaxImages;
    }
} elseif (isset($_SESSION['global_max_images']) && intval($_SESSION['global_max_images']) > 0) {
    // Fallback to session if config file doesn't exist
    $globalMaxImages = intval($_SESSION['global_max_images']);
}

// Get current page from query string
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 7; // Number of products per page

// Get paginated products
try {
    $result = $productModel->getAllActiveProducts($currentPage, $perPage);
    $products = $result['products'] ?? [];
    $totalPages = $result['pages'] ?? 1;
    
    // If no products found or error occurred, set defaults
    if (empty($products)) {
        $products = [];
        $totalPages = 1;
    }
} catch (Exception $e) {
    // Log the error and set default values
    error_log("Error fetching products: " . $e->getMessage());
    $products = [];
    $totalPages = 1;
}

$currentUser = $authController->getCurrentUser();

require_once '../components/Sidebar.php';
$sidebar = new Sidebar();
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
    .body-wrapper { margin-left: 260px; background: #f8f9fa; min-height: 100vh; }
    .app-header { background: #fff; border-bottom: 1px solid #eee; }
    .navbar .navbar-nav .nav-link img { border: 2px solid #dee2e6; }
    .dropdown-menu { min-width: 220px; }
    .btn-delete-product { margin-left: 8px; }
    .config-info {
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 0.375rem;
      padding: 1rem;
      margin-top: 1rem;
    }
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
      <!-- Success Messages -->
      <?php if (isset($globalUpdateMessage)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="ti ti-check me-2"></i>
          <?= htmlspecialchars($globalUpdateMessage) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
      
      <?php if (isset($productUpdateMessage)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="ti ti-check me-2"></i>
          <?= htmlspecialchars($productUpdateMessage) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>


      <!-- Products List -->
      <div class="card">
        <div class="card-body">
          <h5 class="card-title fw-semibold mb-4">
            <i class="ti ti-package me-2"></i>
            Product List
          </h5>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Image</th>
                  <th>Title</th>
                  <th>Description</th>
                  <th>Supplier</th>
                  <th>Price</th>
                  <th>Max Images</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($products)): ?>
                <tr>
                  <td colspan="7" class="text-center py-4">
                    <i class="ti ti-mood-empty fs-4 mb-2 d-block"></i>
                    No products found
                  </td>
                </tr>
                <?php else: ?>
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
                  <td><?= htmlspecialchars(substr($product['description'], 0, 50)) ?>...</td>
                  <td><?= htmlspecialchars($product['supplier_name'] ?? 'Unknown') ?></td>
                  <td><?= number_format($product['price'], 2) ?> DT</td>
                  <td>
                    <form method="POST" action="products.php" style="display:inline-flex; align-items:center; gap:0.5em;">
                      <input type="hidden" name="update_max_images_id" value="<?= $product['id'] ?>">
                      <input type="number" name="max_product_images" min="1" max="20" 
                             value="<?= htmlspecialchars($product['max_product_images'] ?? $globalMaxImages) ?>" 
                             style="width: 60px;" class="form-control form-control-sm">
                      <button type="submit" class="btn btn-xs btn-outline-primary" title="Update Max Images">
                        <i class="ti ti-check"></i>
                      </button>
                    </form>
                  </td>
                  <td>
                    <a href="product-details.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-info">
                      <i class="ti ti-eye me-1"></i>
                      View
                    </a>
                    <form method="POST" action="products.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                      <input type="hidden" name="delete_product_id" value="<?= $product['id'] ?>">
                      <button type="submit" class="btn btn-sm btn-outline-danger btn-delete-product" title="Delete Product">
                        <i class="ti ti-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
              <nav aria-label="Product navigation">
                <ul class="pagination">
                  <?php if ($currentPage > 1): ?>
                    <li class="page-item">
                      <a class="page-link" href="?page=<?= $currentPage - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                      </a>
                    </li>
                  <?php endif; ?>
                  
                  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                      <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                  <?php endfor; ?>
                  
                  <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                      <a class="page-link" href="?page=<?= $currentPage + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                      </a>
                    </li>
                  <?php endif; ?>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>

      <!-- Global Max Images Setting -->
      <div class="card mt-4">
        <div class="card-body">
          <h6 class="card-title fw-semibold mb-3">
            <i class="ti ti-settings me-2"></i>
            Global Maximum Images Setting
          </h6>
          <form method="POST" action="products.php" class="d-flex align-items-center flex-wrap" style="gap: 1em;">
            <input type="hidden" name="set_global_max_images" value="1">
            <div class="d-flex align-items-center" style="gap: 0.5em;">
              <label for="global_max_images" class="mb-0 fw-medium">Default Max Images for All New Products:</label>
              <input type="number" id="global_max_images" name="global_max_images" min="1" max="20" value="<?= $globalMaxImages ?>" style="width: 80px;" class="form-control form-control-sm">
              <button type="submit" class="btn btn-sm btn-primary">
                <i class="ti ti-device-floppy me-1"></i>
                Save
              </button>
            </div>
          </form>
          
          <div class="config-info mt-3">
            <small class="text-muted">
              <strong>Current Setting:</strong> Maximum <?= $globalMaxImages ?> images per product<br>
              <em>This setting applies to all new products. Existing products can have individual limits set above.</em>
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>