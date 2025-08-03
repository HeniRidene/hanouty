<?php
// Show error message if present in URL
if (isset($_GET['error'])) {
    $errorMsg = '';
    switch ($_GET['error']) {
        case 'invalid_supplier': $errorMsg = 'Supplier ID is invalid.'; break;
        case 'supplier_not_found': $errorMsg = 'Supplier not found.'; break;
        case 'invalid_spot': $errorMsg = 'Invalid spot number.'; break;
        case 'db_connection': $errorMsg = 'Database connection failed.'; break;
        default: $errorMsg = 'An unknown error occurred.';
    }
    echo '<div style="color:red;text-align:center;margin:1em 0;">' . htmlspecialchars($errorMsg) . '</div>';
}

require_once '../../../controller/AuthController.php';
$authController = new AuthController();
if (!$authController->isLoggedIn() || !$authController->isSupplier()) {
    header('Location: authentication-login.php');
    exit();
}

// Database connection with error handling
try {
    $mysqli = new mysqli('localhost', 'root', '', 'hanouty');
    if ($mysqli->connect_errno) {
        throw new Exception('Database connection failed: ' . $mysqli->connect_error);
    }
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Database connection failed. Please try again later.']);
        exit;
    }
    $featuredPage = isset($_GET['featured_page']) ? (int)$_GET['featured_page'] : 1;
    header('Location: buy_spot.php?featured_page=' . $featuredPage . '&error=db_connection');
    exit;
}

// Determine supplier ID
if ($authController->isSupplier()) {
    // For suppliers, get ID from session
    $currentUser = $authController->getCurrentUser();
    $supplierId = $currentUser['id'];
} else {
    // For admins, allow POST
    $supplierId = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
}
$featuredPage = isset($_POST['featured_page']) ? (int)$_POST['featured_page'] : 1;
$spot = isset($_POST['spot']) ? (int)$_POST['spot'] : 0;

// Validate inputs
if ($supplierId <= 0) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Invalid supplier ID.']);
        exit;
    }
    // For suppliers, this should never happen; for admins, show error on page
    header('Location: buy_spot.php?error=invalid_supplier');
    exit;
}

if ($featuredPage < 1) {
    $featuredPage = 1;
}

if ($spot < 1 || $spot > 10) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Invalid spot number. Please select a valid spot (1-10).']);
        exit;
    }
    header('Location: buy_spot.php?featured_page=' . $featuredPage . '&error=invalid_spot');
    exit;
}

try {
    // Verify supplier exists
    $stmt = $mysqli->prepare('SELECT user_id FROM supplier WHERE user_id = ?');
    if (!$stmt) {
        throw new Exception('Failed to prepare supplier check statement: ' . $mysqli->error);
    }
    
    $stmt->bind_param('i', $supplierId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result->fetch_assoc()) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Supplier not found.']);
            exit;
        }
        header('Location: buy_spot.php?error=supplier_not_found');
        exit;
    }
    $stmt->close();

    // Get system supplier ID and spot prices
    $systemSupplierId = null;
    $stmt = $mysqli->query("SELECT user_id FROM supplier WHERE business_name = 'System Default'");
    if ($stmt && $systemSupplier = $stmt->fetch_assoc()) {
        $systemSupplierId = $systemSupplier['user_id'];
        
        // Get current spot price
        $spotPriceResult = $mysqli->prepare('SELECT price_paid FROM featured_spots WHERE page_number = ? AND spot_number = ? AND supplier_id = ? AND end_date = "2099-12-31 23:59:59"');
        if ($spotPriceResult) {
            $spotPriceResult->bind_param('iii', $featuredPage, $spot, $systemSupplierId);
            $spotPriceResult->execute();
            $priceResult = $spotPriceResult->get_result();
            $spotPrice = $priceResult->fetch_assoc()['price_paid'];
            $spotPriceResult->close();
        }
    }

    // Start transaction
    $mysqli->autocommit(FALSE);
    
    // Check if spot is available
    $stmt = $mysqli->prepare('SELECT id FROM featured_spots WHERE page_number = ? AND spot_number = ? AND supplier_id != ? AND end_date > NOW()');
    if (!$stmt) {
        throw new Exception('Failed to prepare spot check statement: ' . $mysqli->error);
    }
    
    $stmt->bind_param('iii', $featuredPage, $spot, $systemSupplierId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        throw new Exception('Spot is already occupied.');
    }
    $stmt->close();

    // Insert new spot purchase
    $stmt = $mysqli->prepare('INSERT INTO featured_spots (supplier_id, page_number, spot_number, price_paid, start_date, end_date) VALUES (?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY))');
    if (!$stmt) {
        throw new Exception('Failed to prepare insert statement: ' . $mysqli->error);
    }
    
    $stmt->bind_param('iiii', $supplierId, $featuredPage, $spot, $spotPrice);
    if (!$stmt->execute()) {
        throw new Exception('Failed to insert spot purchase: ' . $stmt->error);
    }
    $stmt->close();

    // Commit transaction
    $mysqli->commit();
    $mysqli->autocommit(TRUE);
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Spot purchased successfully.']);
        exit;
    }
    
    header('Location: router.php?featured_page=' . $featuredPage . '&success=spot_purchased');
    exit;

} catch (Exception $e) {
    // Rollback transaction if it was started
    if (isset($mysqli)) {
        $mysqli->rollback();
        $mysqli->autocommit(TRUE);
    }
    
    error_log("Spot purchase error: " . $e->getMessage());
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
    
    header('Location: router.php?featured_page=' . $featuredPage . '&error=spot_purchase_failed');
    exit;
}
