<?php
require_once '../../../controller/AuthController.php';
require_once '../../../controller/UserController.php';

// Initialize default spot prices
$spotPrices = [1=>100, 2=>90, 3=>80, 4=>70, 5=>60, 6=>50, 7=>40, 8=>30, 9=>20, 10=>10];

// Function to ensure system supplier exists
function ensureSystemSupplier($pdo) {
    // First, try to find the system supplier
    $stmt = $pdo->query("SELECT s.user_id FROM supplier s JOIN users u ON s.user_id = u.id WHERE s.business_name = 'System Default'");
    $systemSupplierId = $stmt->fetch(PDO::FETCH_COLUMN);
    
    if (!$systemSupplierId) {
        $pdo->beginTransaction();
        try {
            // Create system user
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES ('System', 'system@hanouty.com', ?, 'supplier', NOW())");
            $stmt->execute([password_hash('systempassword123', PASSWORD_DEFAULT)]);
            $systemUserId = $pdo->lastInsertId();
            
            // Create system supplier
            $stmt = $pdo->prepare("INSERT INTO supplier (user_id, business_name) VALUES (?, 'System Default')");
            $stmt->execute([$systemUserId]);
            
            $pdo->commit();
            return $systemUserId;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw new Exception('Failed to create system supplier: ' . $e->getMessage());
        }
    }
    return $systemSupplierId;
}

// Get current spot prices from database
try {
    $pdo = new PDO('mysql:host=localhost;dbname=hanouty', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ensure system supplier exists
    $systemSupplierId = ensureSystemSupplier($pdo);
    
    // Now get the spot prices using the system supplier ID
    $stmt = $pdo->prepare('SELECT spot_number, price_paid FROM featured_spots WHERE page_number=0 AND supplier_id = ? ORDER BY start_date DESC');
    $stmt->execute([$systemSupplierId]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $spotPrices[(int)$row['spot_number']] = (float)$row['price_paid'];
    }
} catch(PDOException $e) {
    $message = "Database error: " . $e->getMessage();
    $messageType = 'danger';
}

$authController = new AuthController();
$userController = new UserController();

// Check if user is logged in
if (!$authController->isLoggedIn()) {
    header('Location: authentication-login.php');
    exit();
}

// Check if user is admin
if (!$authController->isAdmin()) {
    $currentUser = $authController->getCurrentUser();
    $message = "Welcome " . $currentUser['name'] . "! You don't have admin access.";
}

// Handle form submissions
$message = '';
$messageType = '';

// Handle user creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $result = $userController->handleUserForm();
    if ($result) {
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }
}

// Handle user update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $result = $userController->handleUserForm();
    if ($result) {
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }
}

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $result = $userController->handleDeleteUser();
    if ($result) {
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }
}

// Handle spot price update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_spot_prices') {
    $spotPrices = $_POST['spot_prices'] ?? [];
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=hanouty', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        foreach ($spotPrices as $spot => $price) {
            $spot = (int)$spot;
            $price = (float)$price;
            // Use page_number=0 and supplier_id=NULL for global spot prices
            $now = date('Y-m-d H:i:s');
            $forever = '2099-12-31 23:59:59';  // Far future date for global prices
            
            // Ensure system supplier exists and get ID
            $systemSupplierId = ensureSystemSupplier($pdo);
            
            $stmt = $pdo->prepare('REPLACE INTO featured_spots (page_number, spot_number, supplier_id, start_date, end_date, price_paid) VALUES (0, :spot, :supplier_id, :start_date, :end_date, :price)');
            $stmt->execute([
                ':spot' => $spot,
                ':supplier_id' => $systemSupplierId,
                ':start_date' => $now,
                ':end_date' => $forever,
                ':price' => $price
            ]);
        }
        
        $message = 'Spot prices updated successfully!';
        $messageType = 'success';
    } catch (PDOException $e) {
        $message = 'Error updating spot prices: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Get all users with details
$users = $userController->getAllUsersWithDetails();
if (isset($users['error'])) {
    $message = $users['error'];
    $messageType = 'danger';
    $users = [];
}

// Get current spot prices from featured_spots (already loaded at the top of the file)
try {
    $pdo = new PDO('mysql:host=localhost;dbname=hanouty', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ensure system supplier exists and get ID
    $systemSupplierId = ensureSystemSupplier($pdo);
    
    if ($systemSupplierId) {
        $stmt = $pdo->prepare('SELECT spot_number, price_paid FROM featured_spots WHERE page_number=0 AND supplier_id = ? ORDER BY start_date DESC');
        $stmt->execute([$systemSupplierId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $spotPrices[(int)$row['spot_number']] = (float)$row['price_paid'];
        }
    }
} catch(PDOException $e) {
    // Silently fail and use default prices if there's an error
}
$currentUser = $authController->getCurrentUser();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hanouty Admin - User Management</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
<div class="body-wrapper">
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
      <div class="col-lg-12">
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title fw-semibold">User Management</h5>
            <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>" role="alert">
              <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Business/Address</th>
                    <th>Created</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($users as $user): ?>
                  <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                      <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'supplier' ? 'warning' : 'info'); ?>">
                        <?php echo ucfirst($user['role']); ?>
                      </span>
                    </td>
                    <td>
                      <?php if ($user['role'] === 'supplier' && !empty($user['business_name'])): ?>
                        <?php echo htmlspecialchars($user['business_name']); ?>
                      <?php elseif ($user['role'] === 'client' && !empty($user['address'])): ?>
                        <?php echo htmlspecialchars($user['address']); ?>
                      <?php else: ?>
                        -
                      <?php endif; ?>
                    </td>
                    <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <h5 class="card-title fw-semibold">Edit Spot Prices</h5>
            <form method="POST" action="">
              <input type="hidden" name="action" value="update_spot_prices">
              <div class="row">
                <?php for ($i = 1; $i <= 10; $i++): ?>
                <div class="col-md-2 mb-3">
                  <label for="spot_price_<?php echo $i; ?>" class="form-label">Spot <?php echo $i; ?></label>
                  <input type="number" step="0.01" min="0" class="form-control" id="spot_price_<?php echo $i; ?>" name="spot_prices[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($spotPrices[$i]); ?>">
                </div>
                <?php endfor; ?>
              </div>
              <button type="submit" class="btn btn-primary">Save Prices</button>
            </form>
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