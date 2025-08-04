<?php
require_once '../../../controller/AuthController.php';
$authController = new AuthController();

// Initialize messages
$errorMsg = '';
$successMsg = '';

// Check authentication first
if (!$authController->isLoggedIn() || !$authController->isSupplier()) {
    header('Location: authentication-login.php');
    exit();
}

$currentUser = $authController->getCurrentUser();

// Get current page number
$currentPage = isset($_GET['featured_page']) ? (int)$_GET['featured_page'] : 1;
if ($currentPage < 1) $currentPage = 1;

// Process spot purchase if requested
if (isset($_GET['action']) && $_GET['action'] === 'buy' && isset($_GET['spot'])) {
    $spot = (int)$_GET['spot'];
    $userSupplierId = $_SESSION['user_id'];
    
    if ($spot < 1 || $spot > 10) {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?error=invalid_spot&featured_page=' . $currentPage);
        exit();
    }
    
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=hanouty", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Start transaction
        $pdo->beginTransaction();
        
        // Get system supplier ID and spot prices first
        $stmt = $pdo->prepare("SELECT user_id FROM supplier WHERE business_name = 'System Default'");
        $stmt->execute();
        $systemSupplier = $stmt->fetch(PDO::FETCH_ASSOC);
        $systemSupplierId = $systemSupplier ? $systemSupplier['user_id'] : null;
        
        // Verify supplier record exists
        $stmt = $pdo->prepare('SELECT user_id FROM supplier WHERE user_id = ?');
        $stmt->execute([$userSupplierId]);
        
        if (!$stmt->fetch()) {
            // Create supplier record if not exists
            $stmt = $pdo->prepare('SELECT name FROM users WHERE id = ?');
            $stmt->execute([$userSupplierId]);
            $userName = 'Supplier Business';
            if ($userRow = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $userName = $userRow['name'] . ' Business';
            }
            
            $stmt = $pdo->prepare('INSERT INTO supplier (user_id, business_name, bio) VALUES (?, ?, ?)');
            $defaultBio = 'New supplier';
            if (!$stmt->execute([$userSupplierId, $userName, $defaultBio])) {
                throw new Exception('Failed to create supplier record');
            }
        }

        // Check if spot is taken by ANY supplier (excluding price configuration records)
        $now = date('Y-m-d H:i:s');
        
        // Check for active spots - exclude system price configuration records
        $stmt = $pdo->prepare('SELECT supplier_id, end_date FROM featured_spots 
                             WHERE page_number = ? AND spot_number = ? AND end_date > ? 
                             AND end_date != "2099-12-31 23:59:59" 
                             ORDER BY end_date DESC LIMIT 1 FOR UPDATE');
        $stmt->execute([$featuredPage, $spot, $now]);
        
        if ($stmt->fetch()) {
            $pdo->rollBack();
            header('Location: ' . $_SERVER['PHP_SELF'] . '?error=spot_taken&featured_page=' . $currentPage);
            exit();
        }
        
        // Insert new spot purchase
        $expiry = date('Y-m-d H:i:s', strtotime('+3 days')); // Using 3 days as in front office
        // Get price from system configuration
        $stmt = $pdo->prepare("SELECT price_paid FROM featured_spots 
                              WHERE page_number = ? AND spot_number = ? AND supplier_id = ? 
                              AND end_date = '2099-12-31 23:59:59'");
        $stmt->execute([$currentPage, $spot, $systemSupplierId]);
        $priceRow = $stmt->fetch(PDO::FETCH_ASSOC);
        $price = $priceRow ? $priceRow['price_paid'] : 100; // Use system price or default to 100
        
        $stmt = $pdo->prepare("INSERT INTO featured_spots (supplier_id, page_number, spot_number, start_date, end_date, price_paid) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userSupplierId, $currentPage, $spot, $now, $expiry, $price]);
        
        $pdo->commit();
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=spot_purchased&featured_page=' . $currentPage);
        
    } catch (PDOException $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log($e->getMessage());
        header('Location: ' . $_SERVER['PHP_SELF'] . '?error=database_error&featured_page=' . $currentPage);
        exit();
    }
}

// Get error/success messages from URL parameters
if (isset($_GET['error'])) {
    switch($_GET['error']) {
        case 'invalid_spot':
            $errorMsg = 'Invalid spot number. Please select a valid spot (1-10).';
            break;
        case 'spot_taken':
            $errorMsg = 'This spot is already taken.';
            break;
        case 'database_error':
            $errorMsg = 'Database error occurred. Please try again later.';
            break;
    }
}

if (isset($_GET['success']) && $_GET['success'] === 'spot_purchased') {
    $successMsg = 'Spot purchased successfully!';
}

// Get all taken spots for display
try {
    $pdo = new PDO("mysql:host=localhost;dbname=hanouty", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $takenSpots = [];
    $stmt = $pdo->prepare("SELECT spot_number, supplier_id FROM featured_spots 
                          WHERE page_number = ? AND end_date > NOW() 
                          AND end_date != '2099-12-31 23:59:59'");
    $stmt->execute([$currentPage]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $takenSpots[$row['spot_number']] = $row['supplier_id'];
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    $errorMsg = 'Error loading spots. Please try again later.';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Buy Featured Spot - Hanouty</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
<?php 
require_once '../components/supplier_sidebar.php';
$sidebar = new SupplierSidebar();
echo $sidebar->render();
?>
<div class="body-wrapper" style="margin-left:260px; padding: 20px;">
  <header class="app-header bg-white mb-4" style="margin: -20px -20px 20px -20px; padding: 0 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
    <nav class="navbar navbar-expand-lg navbar-light">
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

<?php
// Get all taken spots for display
try {
    $pdo = new PDO("mysql:host=localhost;dbname=hanouty", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $now = date('Y-m-d H:i:s');
    
    // Get system supplier ID and spot prices first
    $stmt = $pdo->prepare("SELECT user_id FROM supplier WHERE business_name = 'System Default'");
    $stmt->execute();
    $systemSupplier = $stmt->fetch(PDO::FETCH_ASSOC);
    $systemSupplierId = $systemSupplier ? $systemSupplier['user_id'] : null;
    
    // Get spot prices from system configuration
    $spotPrices = [];
    if ($systemSupplierId) {
        $stmt = $pdo->prepare("SELECT spot_number, price_paid FROM featured_spots 
                              WHERE page_number = ? AND supplier_id = ? 
                              AND end_date = '2099-12-31 23:59:59'");
        $stmt->execute([$currentPage, $systemSupplierId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $spotPrices[$row['spot_number']] = $row['price_paid'];
        }
    }
    
    // Get all taken spots for display - exclude system price configuration records
    $takenSpots = [];
    $stmt = $pdo->prepare("SELECT spot_number, supplier_id FROM featured_spots 
                          WHERE page_number = ? AND end_date > ? 
                          AND end_date != '2099-12-31 23:59:59'");
    $stmt->execute([$currentPage, $now]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $takenSpots[$row['spot_number']] = $row['supplier_id'];
    }
    
} catch (PDOException $e) {
    error_log($e->getMessage());
    $errorMsg = 'Error loading spots. Please try again later.';
}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Buy Featured Spot</h4>
                    
                    <?php if ($errorMsg): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($errorMsg); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($successMsg): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div>
                    <?php endif; ?>
                    
                    <div class="featured-spots-grid">
                        <?php for ($i = 1; $i <= 10; $i++): 
                            $isSpotTaken = isset($takenSpots[$i]);
                            $isOwnSpot = $isSpotTaken && $takenSpots[$i] == $_SESSION['user_id'];
                        ?>
                            <div class="spot-card <?php echo $isSpotTaken ? 'taken' : ''; ?> <?php echo $isOwnSpot ? 'own-spot' : ''; ?>">
                                <h5>Spot <?php echo $i; ?></h5>
                                <?php if (!$isSpotTaken): ?>
                                    <div class="price-tag mb-2">
                                        <span class="badge bg-info">
                                            Price: $<?php echo isset($spotPrices[$i]) ? number_format($spotPrices[$i], 2) : '0.00'; ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($isOwnSpot): ?>
                                    <span class="badge bg-success mb-2">Your Spot</span>
                                    <a href="add_product.php?spot=<?php echo $i; ?>&page=<?php echo $currentPage; ?>" 
                                       class="btn btn-primary">Add Product</a>
                                <?php elseif ($isSpotTaken): ?>
                                    <button class="btn btn-secondary" disabled>Spot Taken</button>
                                <?php else: ?>
                                    <a href="?action=buy&spot=<?php echo $i; ?>&featured_page=<?php echo $currentPage; ?>" 
                                       class="btn btn-primary">Buy Spot</a>
                                <?php endif; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="pagination-container mt-4">
                        <nav>
                            <ul class="pagination">
                                <?php
                                $totalPages = 5; // Set your total pages here
                                for ($p = 1; $p <= $totalPages; $p++): ?>
                                    <li class="page-item <?php echo $p == $currentPage ? 'active' : ''; ?>">
                                        <a class="page-link" href="?featured_page=<?php echo $p; ?>">
                                            <?php echo $p; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.featured-spots-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    padding: 20px 0;
}

.spot-card {
    border: 1px solid #ddd;
    padding: 15px;
    text-align: center;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.spot-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.spot-card.taken {
    background: #f8f9fa;
}

.spot-card.own-spot {
    border-color: #198754;
    background: #f8fff9;
}

.badge {
    display: block;
    margin-bottom: 10px;
}
</style>

    </div> <!-- End of body-wrapper -->
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/sidebarmenu.js"></script>
    <script src="../assets/js/app.min.js"></script>
</body>
</html>