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
$product = null;
if (isset($_GET['id'])) {
    $product = $productModel->getById($_GET['id']);
}
if (!$product) {
    echo '<div class="alert alert-danger m-5">Product not found.</div>';
    exit();
}
$images = $product['images'] ? json_decode($product['images'], true) : [];
$mainImage = $images[0] ?? 'https://dummyimage.com/450x300/dee2e6/6c757d.jpg';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Product Details - Hanouty Admin</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <style>
    .product-img-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; cursor:pointer; border:2px solid #eee; margin-right: 6px; }
    .selected-thumb { border: 2px solid #198754 !important; box-shadow: 0 0 0 2px #19875433; }
    #main-image-container { width: 100%; height: 400px; display: flex; align-items: center; justify-content: center; background: #fafafa; border-radius: 0.5rem; margin-bottom: 10px; }
    #main-product-image { max-width: 100%; max-height: 100%; object-fit: contain; display: block; }
    .left-sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 220px; background: #fff; border-right: 1px solid #eee; z-index: 1040; }
    .nav.flex-column { margin-top: 2rem; }
    .nav.flex-column .nav-link { color: #333; font-weight: 500; padding: 0.75rem 1.5rem; border-radius: 0.5rem; margin-bottom: 0.5rem; transition: background 0.2s, color 0.2s; }
    .nav.flex-column .nav-link.active, .nav.flex-column .nav-link:hover { background: #f1f3f4; color: #198754; }
    .nav.flex-column .nav-link i { margin-right: 0.7rem; }
    .body-wrapper { margin-left: 220px; background: #f8f9fa; min-height: 100vh; }
    .app-header { background: #fff; border-bottom: 1px solid #eee; }
    .navbar .navbar-nav .nav-link img { border: 2px solid #dee2e6; }
    .dropdown-menu { min-width: 220px; }
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
          <a class="nav-link" href="products.php">
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
    <div class="container py-5">
      <a href="products.php" class="btn btn-outline-dark mb-4">&larr; Back to Products</a>
      <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
          <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <?php if ($mainImage): ?>
            <div class="bg-light d-flex align-items-center justify-content-center" style="height:320px;">
              <img id="main-product-image" src="<?= htmlspecialchars($mainImage) ?>" style="max-height: 300px; max-width: 100%; object-fit: contain; border-radius: 1rem; box-shadow: 0 2px 8px #0001;">
            </div>
            <?php endif; ?>
            <div class="card-body p-4">
              <h2 class="card-title mb-2 text-primary fw-bold" style="font-size:2rem; letter-spacing:0.5px;"><?= htmlspecialchars($product['title']) ?></h2>
              <div class="mb-2 text-success fw-bold fs-4"><?= number_format($product['price'], 2) ?> DT</div>
              <?php if (!empty($product['category'])): ?>
                <span class="badge bg-success mb-3">Category: <?= htmlspecialchars($product['category']) ?></span><br>
              <?php endif; ?>
              <p class="card-text mb-4 text-secondary" style="font-size:1.1rem; line-height:1.6; min-height:60px;">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
              </p>
              <?php if (count($images) > 1): ?>
                <div class="d-flex flex-wrap gap-2 mb-3">
                  <?php foreach ($images as $idx => $img): ?>
                    <img src="<?= htmlspecialchars($img) ?>" class="product-img-thumb<?= $idx === 0 ? ' selected-thumb' : '' ?>" alt="" data-img="<?= htmlspecialchars($img) ?>">
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
              <hr>
              <div class="d-flex align-items-center mt-3">
                <?php if (!empty($product['profile_image'])): ?>
                  <img src="<?= htmlspecialchars($product['profile_image']) ?>" alt="Supplier" class="rounded-circle me-2" width="40" height="40">
                <?php endif; ?>
                <div>
                  <?php
                  $supplierDisplayName = !empty($product['business_name']) ? $product['business_name'] : (!empty($product['supplier_name']) ? $product['supplier_name'] : 'Unknown Supplier');
                  ?>
                  <div class="fw-bold text-dark">Supplier: <?= htmlspecialchars($supplierDisplayName) ?></div>
                  <?php if (!empty($product['bio'])): ?>
                    <small class="text-muted">Bio: <?= htmlspecialchars($product['bio']) ?></small>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Image thumbnail click to change main image
    const mainImg = document.getElementById('main-product-image');
    document.querySelectorAll('.product-img-thumb').forEach(function(thumb) {
      thumb.addEventListener('click', function() {
        mainImg.src = this.getAttribute('data-img');
        document.querySelectorAll('.product-img-thumb').forEach(t => t.classList.remove('selected-thumb'));
        this.classList.add('selected-thumb');
      });
    });
  </script>
</body>
</html> 