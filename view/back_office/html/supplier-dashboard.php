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

require_once '../components/Sidebar.php';
$sidebar = new Sidebar();

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
    <?php echo $sidebar->render(); ?>
    <div class="body-wrapper" style="margin-left: 260px; padding: 20px;">
        <header class="app-header" style="margin: -20px -20px 20px -20px;">
            <nav class="navbar navbar-expand-lg navbar-light bg-white px-3">
                <ul class="navbar-nav">
                    <li class="nav-item d-block d-xl-none">
                        <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </header>
        <div class="container-fluid">
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
</body>
</html> 