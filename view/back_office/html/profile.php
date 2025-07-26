<?php
require_once '../../../controller/AuthController.php';
require_once '../../../controller/UserController.php';

$authController = new AuthController();
$userController = new UserController();

// Check if user is logged in
if (!$authController->isLoggedIn()) {
    header('Location: authentication-login.php');
    exit();
}

// Get user ID from URL parameter
$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get user details
$user = $userController->getUserById($userId);
if (!$user) {
    header('Location: user-management.php');
    exit();
}

require_once '../components/Sidebar.php';
$sidebar = new Sidebar();

// Get user's products if they are a supplier
$products = [];
$error = null;

if ($user['role'] === 'supplier') {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=hanouty', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // First, check if the user has any products
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM products WHERE user_id = ?');
        $stmt->execute([$userId]);
        $productCount = $stmt->fetchColumn();
        
        if ($productCount > 0) {
            $query = "SELECT 
                    p.id,
                    p.title,
                    p.description,
                    p.price,
                    p.status,
                    p.images,
                    p.created_at,
                    p.user_id
                    FROM products p 
                    WHERE p.user_id = ?
                    ORDER BY p.created_at DESC";
            
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
}

$currentUser = $authController->getCurrentUser();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Profile - <?php echo htmlspecialchars($user['name']); ?></title>
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
                    <li class="nav-item">
                        <a class="nav-link" href="user-management.php">
                            <i class="ti ti-arrow-left"></i> Back to User Management
                        </a>
                    </li>
                </ul>
            </nav>
        </header>

        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center">
                                <img src="../assets/images/profile/user-1.jpg" alt="" class="rounded-circle mb-3" width="150" height="150">
                                <h4 class="fw-semibold"><?php echo htmlspecialchars($user['name']); ?></h4>
                                <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'supplier' ? 'warning' : 'info'); ?> mb-3">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                                <div class="mt-4">
                                    <p class="mb-1"><i class="ti ti-mail me-2"></i><?php echo htmlspecialchars($user['email']); ?></p>
                                    <?php if ($user['role'] === 'supplier' && !empty($user['business_name'])): ?>
                                        <p class="mb-1"><i class="ti ti-building me-2"></i><?php echo htmlspecialchars($user['business_name']); ?></p>
                                    <?php endif; ?>
                                    <?php if ($user['role'] === 'client' && !empty($user['address'])): ?>
                                        <p class="mb-1"><i class="ti ti-map-pin me-2"></i><?php echo htmlspecialchars($user['address']); ?></p>
                                    <?php endif; ?>
                                    <p class="mb-1"><i class="ti ti-calendar me-2"></i>Member since <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <?php if ($user['role'] === 'supplier'): ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">Published Products</h5>
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
                    <?php endif; ?>

                    <?php if ($user['role'] === 'client'): ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">Order History</h5>
                            <!-- Add order history table here when implemented -->
                            <div class="alert alert-info">
                                Order history feature coming soon.
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
