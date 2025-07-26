<?php
require_once '../../../controller/AuthController.php';
require_once '../../../controller/UserController.php';

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

// Initialize spot prices with defaults
$spotPrices = [1=>100, 2=>90, 3=>80, 4=>70, 5=>60, 6=>50, 7=>40, 8=>30, 9=>20, 10=>10];

require_once '../components/Sidebar.php';
$sidebar = new Sidebar();

// Current page for spot prices
$currentPricePage = isset($_GET['price_page']) ? (int)$_GET['price_page'] : 1;

// Get current spot prices from database
try {
    $pdo = new PDO('mysql:host=localhost;dbname=hanouty', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ensure system supplier exists
    $systemSupplierId = ensureSystemSupplier($pdo);
    
    // Now get the spot prices using the system supplier ID for the current page
    $stmt = $pdo->prepare('SELECT spot_number, price_paid FROM featured_spots WHERE page_number = ? AND supplier_id = ? ORDER BY spot_number');
    $stmt->execute([$currentPricePage, $systemSupplierId]);
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
        
        $page = isset($_POST['page_number']) ? (int)$_POST['page_number'] : 1;
        
        foreach ($spotPrices as $spot => $price) {
            $spot = (int)$spot;
            $price = (int)$price; // Convert to integer instead of float
            $now = date('Y-m-d H:i:s');
            $forever = '2099-12-31 23:59:59';  // Far future date for global prices
            
            // Ensure system supplier exists and get ID
            $systemSupplierId = ensureSystemSupplier($pdo);
            
            $stmt = $pdo->prepare('REPLACE INTO featured_spots (page_number, spot_number, supplier_id, start_date, end_date, price_paid) VALUES (:page, :spot, :supplier_id, :start_date, :end_date, :price)');
            $stmt->execute([
                ':page' => $page,
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
<?php echo $sidebar->render(); ?>
<div class="body-wrapper" style="margin-left: 260px; padding: 20px;">
  <header class="app-header" style="margin: -20px -20px 20px -20px;">
    <nav class="navbar navbar-expand-lg navbar-light bg-white px-3">
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
  <div class="container-fluid">
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
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Business/Address</th>
                    <th>Created</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($users as $user): ?>
                  <tr>
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
                    <td>
                      <div class="d-flex gap-2">
                        <a href="profile.php?id=<?php echo $user['id']; ?>" class="btn btn-link text-primary p-0" title="View Profile">
                          <i class="ti ti-user-circle fs-5"></i>
                        </a>
                        <button type="button" class="btn btn-link text-warning p-0" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $user['id']; ?>" title="Edit User">
                          <i class="ti ti-edit fs-5"></i>
                        </button>
                        <button type="button" class="btn btn-link text-danger p-0" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $user['id']; ?>" title="Delete User">
                          <i class="ti ti-trash fs-5"></i>
                        </button>
                      </div>
                      
                      <!-- Profile Modal -->
                      <div class="modal fade" id="profileModal<?php echo $user['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">User Profile</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <div class="row">
                                <div class="col-md-4 border-end">
                                  <div class="text-center p-3">
                                    <img src="../assets/images/profile/user-1.jpg" alt="" class="rounded-circle mb-3" width="100" height="100">
                                    <h5 class="fw-semibold mb-2"><?php echo htmlspecialchars($user['name']); ?></h5>
                                    <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'supplier' ? 'warning' : 'info'); ?>">
                                      <?php echo ucfirst($user['role']); ?>
                                    </span>
                                  </div>
                                </div>
                                <div class="col-md-8">
                                  <h6 class="fw-semibold mb-3">User Information</h6>
                                  <div class="mb-3">
                                    <label class="text-muted mb-1">Email</label>
                                    <p class="mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
                                  </div>
                                  <?php if ($user['role'] === 'supplier'): ?>
                                  <div class="mb-3">
                                    <label class="text-muted mb-1">Business Name</label>
                                    <p class="mb-0"><?php echo htmlspecialchars($user['business_name'] ?? '-'); ?></p>
                                  </div>
                                  <?php endif; ?>
                                  <?php if ($user['role'] === 'client'): ?>
                                  <div class="mb-3">
                                    <label class="text-muted mb-1">Address</label>
                                    <p class="mb-0"><?php echo htmlspecialchars($user['address'] ?? '-'); ?></p>
                                  </div>
                                  <?php endif; ?>
                                  <div class="mb-3">
                                    <label class="text-muted mb-1">Member Since</label>
                                    <p class="mb-0"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                                  </div>
                                </div>
                              </div>
                              <?php if ($user['role'] === 'supplier'): ?>
                              <div class="mt-4">
                                <h6 class="fw-semibold mb-3">Published Products</h6>
                                <div class="table-responsive">
                                  <table class="table table-sm">
                                    <thead>
                                      <tr>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <!-- Add PHP code here to fetch and display user's products -->
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Edit Modal -->
                      <div class="modal fade" id="editModal<?php echo $user['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Edit User</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="POST" action="">
                              <input type="hidden" name="action" value="update">
                              <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                              <div class="modal-body">
                                <!-- Add form fields for editing user information -->
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>

                      <!-- Delete Modal -->
                      <div class="modal fade" id="deleteModal<?php echo $user['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Delete User</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                              <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <a href="featured-spots.php" class="btn btn-primary">Edit Featured Spots</a>
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