<?php
require_once '../../../controller/AuthController.php';
require_once '../../../controller/UserController.php';

$authController = new AuthController();
$userController = new UserController();

// Only allow logged-in suppliers
if (!$authController->isLoggedIn() || !$authController->isSupplier()) {
    header('Location: authentication-login.php');
    exit();
}

$currentUser = $authController->getCurrentUser();
$userId = $currentUser['id'];

// Get supplier's products
$products = [];
$error = null;
try {
    $pdo = new PDO('mysql:host=localhost;dbname=hanouty', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM products WHERE user_id = ?');
    $stmt->execute([$userId]);
    $productCount = $stmt->fetchColumn();
    if ($productCount > 0) {
        $query = "SELECT id, title, description, price, status, images, created_at, user_id FROM products WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$userId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($products)) {
            $error = 'No products found';
        }
    } else {
        $error = 'No products found for this supplier';
    }
} catch(PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
}

// Handle product image upload
$uploadMessage = null;
if (isset($_POST['upload_images']) && isset($_FILES['product_images'])) {
    $uploadDir = '../../../uploads/products/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $uploaded = 0;
    foreach ($_FILES['product_images']['tmp_name'] as $idx => $tmpName) {
        $type = $_FILES['product_images']['type'][$idx];
        $name = $_FILES['product_images']['name'][$idx];
        if (in_array($type, $allowedTypes)) {
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $newName = $userId . '_' . uniqid('', true) . '.' . $ext;
            $dest = $uploadDir . $newName;
            if (move_uploaded_file($tmpName, $dest)) {
                $uploaded++;
            }
        }
    }
    $uploadMessage = $uploaded > 0 ? "$uploaded image(s) uploaded successfully." : "No valid images uploaded.";
}

// Get supplier's uploaded images
$supplierImages = [];
$dir = '../../../uploads/products/';
if (is_dir($dir)) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if (preg_match('/^' . $userId . '_/', $file)) {
            $supplierImages[] = $file;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Supplier Dashboard - Hanouty</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
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
    
    <div class="body-wrapper">
        <header class="app-header">
            <nav class="navbar navbar-expand-lg navbar-light">
                <ul class="navbar-nav">
                    <li class="nav-item d-block d-xl-none">
                        <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                </ul>
                <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                    <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                        <li class="nav-item dropdown">
                            <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="../assets/images/profile/user-1.jpg" alt="" width="35" height="35" class="rounded-circle">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                                <div class="message-body">
                                    <a href="supplier-profile.php" class="d-flex align-items-center gap-2 dropdown-item">
                                        <i class="ti ti-user fs-6"></i>
                                        <p class="mb-0 fs-3">My Profile</p>
                                    </a>
                                    <a href="/hanouty/view/front_office/router.php" class="d-flex align-items-center gap-2 dropdown-item">
                                        <i class="ti ti-world fs-6"></i>
                                        <p class="mb-0 fs-3">Front Office</p>
                                    </a>
                                    <a href="authentication-login.php?logout=1" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="container-fluid">
        <div class="container-fluid" style="margin-left: 260px; padding: 20px;">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">My Products</h5>
                            <?php if ($error): ?>
                                <div class="alert alert-warning">
                                    <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($products)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Price</th>
                                                <th>Status</th>
                                                <th>Published Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($products as $product): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($product['title']); ?></td>
                                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        switch($product['status']) {
                                                            case 'active':
                                                                echo 'success';
                                                                break;
                                                            case 'pending':
                                                                echo 'warning';
                                                                break;
                                                            case 'rejected':
                                                                echo 'danger';
                                                                break;
                                                        }
                                                    ?>">
                                                        <?php echo ucfirst($product['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M j, Y', strtotime($product['created_at'])); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    No products published yet.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">Upload Images for New Products</h5>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="product_images" class="form-label">Select Images</label>
                                    <input type="file" class="form-control" id="product_images" name="product_images[]" accept="image/*" multiple required>
                                </div>
                                <button type="submit" name="upload_images" class="btn btn-primary">Upload</button>
                            </form>
                            <?php if (isset($uploadMessage)): ?>
                                <div class="alert alert-info mt-3"><?php echo htmlspecialchars($uploadMessage); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (!empty($supplierImages)): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">Your Uploaded Images</h5>
                            <div class="row">
                                <?php foreach ($supplierImages as $img): ?>
                                <div class="col-3 mb-3">
                                    <img src="../../../uploads/products/<?php echo htmlspecialchars($img); ?>" class="img-fluid rounded" style="max-height:120px;">
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 