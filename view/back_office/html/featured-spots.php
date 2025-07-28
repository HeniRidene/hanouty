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

// Function to get system supplier user_id (for use as supplier_id in featured_spots)
function getSystemSupplierUserId($pdo) {
    $stmt = $pdo->query("SELECT s.user_id FROM supplier s JOIN users u ON s.user_id = u.id WHERE s.business_name = 'System Default'");
    return $stmt->fetch(PDO::FETCH_COLUMN);
}

// Initialize spot prices with defaults
$spotPrices = [1=>100, 2=>90, 3=>80, 4=>70, 5=>60, 6=>50, 7=>40, 8=>30, 9=>20, 10=>10];

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
    header('Location: index.php');
    exit();
}

// Current page for spot prices
$currentPricePage = isset($_GET['price_page']) ? (int)$_GET['price_page'] : 1;
$message = '';
$messageType = '';

// Handle spot price update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_spot_prices') {
    $spotPrices = $_POST['spot_prices'] ?? [];
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=hanouty', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $page = isset($_POST['page_number']) ? (int)$_POST['page_number'] : 1;
        
        // Ensure system supplier exists and get supplier.user_id
        ensureSystemSupplier($pdo);
        $systemSupplierId = getSystemSupplierUserId($pdo);
        
        if (!$systemSupplierId) {
            throw new Exception('Could not retrieve system supplier ID');
        }
        
        foreach ($spotPrices as $spot => $price) {
            $spot = (int)$spot;
            $price = (int)$price;
            $now = date('Y-m-d H:i:s');
            $forever = '2099-12-31 23:59:59'; // Special end date for price configuration records
            
            // Delete existing price configuration record for this spot/page
            $stmt = $pdo->prepare('DELETE FROM featured_spots WHERE page_number = ? AND spot_number = ? AND supplier_id = ? AND end_date = "2099-12-31 23:59:59"');
            $stmt->execute([$page, $spot, $systemSupplierId]);
            
            // Insert new price configuration record
            $stmt = $pdo->prepare('INSERT INTO featured_spots (page_number, spot_number, supplier_id, start_date, end_date, price_paid) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$page, $spot, $systemSupplierId, $now, $forever, $price]);
        }
        
        $message = 'Spot prices updated successfully!';
        $messageType = 'success';
    } catch (PDOException $e) {
        $message = 'Error updating spot prices: ' . $e->getMessage();
        $messageType = 'danger';
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Get current spot prices from database
try {
    $pdo = new PDO('mysql:host=localhost;dbname=hanouty', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ensure system supplier exists and get supplier.user_id
    ensureSystemSupplier($pdo);
    $systemSupplierId = getSystemSupplierUserId($pdo);
    
    if ($systemSupplierId) {
        // Get price configuration records (those with end_date = "2099-12-31 23:59:59")
        $stmt = $pdo->prepare('SELECT spot_number, price_paid FROM featured_spots WHERE page_number = ? AND supplier_id = ? AND end_date = "2099-12-31 23:59:59" ORDER BY spot_number');
        $stmt->execute([$currentPricePage, $systemSupplierId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $spotPrices[(int)$row['spot_number']] = (float)$row['price_paid'];
        }
    }
} catch(PDOException $e) {
    $message = "Database error: " . $e->getMessage();
    $messageType = 'danger';
} catch (Exception $e) {
    $message = "Error: " . $e->getMessage();
    $messageType = 'danger';
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
  <title>Hanouty Admin - Featured Spots</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <style>
    .spot-card {
      transition: all 0.3s ease;
      border: 1px solid rgba(0,0,0,0.1);
    }
    .spot-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
    }
    .badge.rounded-circle {
      font-size: 14px;
      font-weight: 500;
    }
    .spots-container {
      max-width: 800px;
      margin: 0 auto;
    }
    .input-group .form-control:focus {
      border-color: #5D87FF;
      box-shadow: none;
    }
    .btn-group .btn {
      min-width: 100px;
    }
    .info-alert {
      background-color: #e3f2fd;
      border: 1px solid #90caf9;
      color: #1565c0;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
    }
    .info-alert h6 {
      color: #0d47a1;
      margin-bottom: 8px;
    }
  </style>
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
      <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
          <li class="nav-item dropdown">
            <a class="nav-link" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown" aria-expanded="false">
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
        <div class="card">
          <div class="card-body">
            <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>" role="alert">
              <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <div class="info-alert">
              <h6><i class="ti ti-info-circle me-2"></i>Featured Spots Configuration</h6>
              <p class="mb-0">
                This panel allows you to set the prices for featured spots on each page. These prices will be charged when suppliers purchase spots.
                Price changes only affect new purchases - existing active spots retain their original price.
              </p>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="card-title fw-semibold mb-0">Spots Configuration - Page <?php echo $currentPricePage; ?></h5>
              <div class="btn-group" role="group">
                <?php 
                $totalPages = 3; // We have 3 pages of prices
                for ($i = 1; $i <= $totalPages; $i++): 
                ?>
                  <a href="?price_page=<?php echo $i; ?>" 
                     class="btn <?php echo $i === $currentPricePage ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    Page <?php echo $i; ?>
                  </a>
                <?php endfor; ?>
              </div>
            </div>

            <form method="POST" action="">
              <input type="hidden" name="action" value="update_spot_prices">
              <input type="hidden" name="page_number" value="<?php echo $currentPricePage; ?>">
              
              <div class="spots-container">
                <?php for ($i = 1; $i <= 10; $i++): ?>
                <div class="card shadow-sm mb-3 spot-card">
                  <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                      <div class="d-flex align-items-center gap-3">
                        <div class="spot-number">
                          <span class="badge bg-primary rounded-circle p-2" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                            <?php echo $i; ?>
                          </span>
                        </div>
                        <div>
                          <h6 class="fw-semibold mb-1">Featured spot <?php echo $i; ?></h6>
                          <small class="text-muted">Premium position on page <?php echo $currentPricePage; ?></small>
                        </div>
                      </div>
                      <div class="price-input" style="width: 200px;">
                        <label for="spot_price_<?php echo $i; ?>" class="form-label small text-muted mb-1">Price</label>
                        <div class="input-group">
                          <span class="input-group-text">$</span>
                          <input type="number" 
                                 step="1" 
                                 min="0" 
                                 class="form-control" 
                                 id="spot_price_<?php echo $i; ?>" 
                                 name="spot_prices[<?php echo $i; ?>]" 
                                 value="<?php echo (int)$spotPrices[$i]; ?>">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php endfor; ?>
              </div>

              <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary px-4">
                  <i class="ti ti-device-floppy me-2"></i>
                  Save Changes
                </button>
              </div>
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