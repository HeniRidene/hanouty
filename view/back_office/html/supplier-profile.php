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

// Handle profile update
$updateMessage = '';
$updateError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=hanouty', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $businessName = trim($_POST['business_name']);
        $bio = trim($_POST['bio']);
        
        // Validate inputs
        if (empty($name) || empty($email)) {
            $updateError = 'Name and email are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $updateError = 'Please enter a valid email address.';
        } else {
            // Update user table
            $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?');
            $stmt->execute([$name, $email, $userId]);
            
            // Update or insert supplier table
            $stmt = $pdo->prepare('SELECT user_id FROM supplier WHERE user_id = ?');
            $stmt->execute([$userId]);
            
            if ($stmt->fetch()) {
                // Update existing supplier record
                $stmt = $pdo->prepare('UPDATE supplier SET business_name = ?, bio = ? WHERE user_id = ?');
                $stmt->execute([$businessName, $bio, $userId]);
            } else {
                // Insert new supplier record
                $stmt = $pdo->prepare('INSERT INTO supplier (user_id, business_name, bio) VALUES (?, ?, ?)');
                $stmt->execute([$userId, $businessName, $bio]);
            }
            
            $updateMessage = 'Profile updated successfully!';
            
            // Update session data
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            
            // Refresh current user data
            $currentUser = $authController->getCurrentUser();
        }
    } catch(PDOException $e) {
        $updateError = 'Database error: ' . $e->getMessage();
    }
}

// Get supplier details
try {
    $pdo = new PDO('mysql:host=localhost;dbname=hanouty', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare('SELECT business_name, bio FROM supplier WHERE user_id = ?');
    $stmt->execute([$userId]);
    $supplierData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $businessName = $supplierData['business_name'] ?? '';
    $bio = $supplierData['bio'] ?? '';
} catch(PDOException $e) {
    $businessName = '';
    $bio = '';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Supplier Profile - Hanouty</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
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
                <div class="container-fluid" style="margin-left: 260px; padding: 20px;">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-person-circle me-2"></i>
                            My Profile
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($updateMessage): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>
                                <?php echo htmlspecialchars($updateMessage); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($updateError): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($updateError); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">
                                            <i class="bi bi-person me-1"></i>
                                            Full Name *
                                        </label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo htmlspecialchars($currentUser['name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">
                                            <i class="bi bi-envelope me-1"></i>
                                            Email Address *
                                        </label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($currentUser['email']); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="business_name" class="form-label">
                                    <i class="bi bi-shop me-1"></i>
                                    Business Name
                                </label>
                                <input type="text" class="form-control" id="business_name" name="business_name" 
                                       value="<?php echo htmlspecialchars($businessName); ?>" 
                                       placeholder="Your business or store name">
                                <div class="form-text">This will be displayed to customers</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="bio" class="form-label">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Business Description
                                </label>
                                <textarea class="form-control" id="bio" name="bio" rows="4" 
                                          placeholder="Tell customers about your business, products, and services"><?php echo htmlspecialchars($bio); ?></textarea>
                                <div class="form-text">A brief description of your business that will be shown to customers</div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="supplier-dashboard.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Back to Dashboard
                                </a>
                                <button type="submit" name="update_profile" class="btn btn-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Account Information Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-shield-check me-2"></i>
                            Account Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Account Type:</strong> <span class="badge bg-success">Supplier</span></p>
                                <br>
                                <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($currentUser['created_at'] ?? 'now')); ?></p>
                                <br>
                                <p><strong>Status:</strong> <span class="badge bg-success">Active</span></p>
                            </div>
                            <div class="col-md-6">  
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 