<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is a supplier
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Authentication required. Please login as a supplier.']);
        exit;
    }
    header('Location: router.php?action=login');
    exit;
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
    header('Location: router.php?featured_page=' . $featuredPage . '&error=db_connection');
    exit;
}

$userSupplierId = $_SESSION['user_id']; // This is the user_id from users table
$featuredPage = isset($_GET['featured_page']) ? (int)$_GET['featured_page'] : 1;
$spot = isset($_GET['spot']) ? (int)$_GET['spot'] : 0;

// Validate inputs
if ($featuredPage < 1) {
    $featuredPage = 1;
}

if ($spot < 1 || $spot > 10) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Invalid spot number. Please select a valid spot (1-10).']);
        exit;
    }
    header('Location: router.php?featured_page=' . $featuredPage . '&error=invalid_spot');
    exit;
}

// Spot prices are always fetched from DB below; no hardcoded prices.

try {
    // Get system supplier ID and fetch current prices
    $systemSupplierId = null;
    $stmt = $mysqli->query("SELECT user_id FROM supplier WHERE business_name = 'System Default'");
    if ($stmt && $systemSupplier = $stmt->fetch_assoc()) {
        $systemSupplierId = $systemSupplier['user_id']; // Use user_id, not id
        
        // Get current prices from system records - these are price configurations, not actual purchases
        $spotPriceResult = $mysqli->prepare('SELECT spot_number, price_paid FROM featured_spots WHERE page_number = ? AND supplier_id = ? AND end_date = "2099-12-31 23:59:59"');
        if ($spotPriceResult) {
            $spotPriceResult->bind_param('ii', $featuredPage, $systemSupplierId);
            $spotPriceResult->execute();
            $result = $spotPriceResult->get_result();
            while ($row = $result->fetch_assoc()) {
                // This part is now redundant as prices are fetched directly from DB
                // $spotPrices[(int)$row['spot_number']] = (int)$row['price_paid']; 
            }
            $spotPriceResult->close();
        }
    }

    // ENSURE supplier record exists FIRST before doing anything else
    $stmt = $mysqli->prepare('SELECT user_id FROM supplier WHERE user_id = ?');
    if (!$stmt) {
        throw new Exception('Failed to prepare supplier check statement: ' . $mysqli->error);
    }
    
    $stmt->bind_param('i', $userSupplierId);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result->fetch_assoc()) {
        // Create supplier record if not exists
        $stmt->close();
        
        // Get user name for business name
        $userStmt = $mysqli->prepare('SELECT name FROM users WHERE id = ?');
        if (!$userStmt) {
            throw new Exception('Failed to prepare user check statement: ' . $mysqli->error);
        }
        
        $userStmt->bind_param('i', $userSupplierId);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        $userName = 'Supplier Business';
        if ($userRow = $userResult->fetch_assoc()) {
            $userName = $userRow['name'] . ' Business';
        }
        $userStmt->close();
        
        $stmt2 = $mysqli->prepare('INSERT INTO supplier (user_id, business_name, bio) VALUES (?, ?, ?)');
        if (!$stmt2) {
            throw new Exception('Failed to prepare supplier insert statement: ' . $mysqli->error);
        }
        
        $defaultBio = 'New supplier';
        $stmt2->bind_param('iss', $userSupplierId, $userName, $defaultBio);
        
        if (!$stmt2->execute()) {
            $stmt2->close();
            throw new Exception('Failed to create supplier record: ' . $stmt2->error);
        }
        $stmt2->close();
    } else {
        $stmt->close();
    }

    // Check if spot is taken by ANY supplier (excluding price configuration records)
    $now = date('Y-m-d H:i:s');

    // Use transaction to prevent race conditions
    $mysqli->autocommit(FALSE);

    // Check for active spots - exclude system price configuration records
    $stmt = $mysqli->prepare('SELECT supplier_id, end_date FROM featured_spots WHERE page_number = ? AND spot_number = ? AND end_date > ? AND end_date != "2099-12-31 23:59:59" ORDER BY end_date DESC LIMIT 1 FOR UPDATE');
    if (!$stmt) {
        throw new Exception('Failed to prepare spot check statement: ' . $mysqli->error);
    }
    
    $stmt->bind_param('iis', $featuredPage, $spot, $now);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $stmt->close();
        $mysqli->rollback();
        $mysqli->autocommit(TRUE);
        $mysqli->close();
        
        // Spot is taken - return appropriate response
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'This spot is already taken by another supplier. Please choose a different spot.']);
            exit;
        }
        header('Location: router.php?featured_page=' . $featuredPage . '&error=spot_taken');
        exit;
    }
    $stmt->close();

    // Insert new spot ownership using supplier.user_id (not supplier.id)
    $expiry = date('Y-m-d H:i:s', strtotime('+3 days'));
    // Spot prices are always fetched from DB below; no hardcoded prices.
    $pricePaid = $spotPrices[$spot] ?? 0; // This line is now redundant as prices are fetched from DB

    $stmt = $mysqli->prepare('INSERT INTO featured_spots (supplier_id, page_number, spot_number, start_date, end_date, price_paid) VALUES (?, ?, ?, ?, ?, ?)');
    if (!$stmt) {
        throw new Exception('Failed to prepare spot insert statement: ' . $mysqli->error);
    }
    
    $stmt->bind_param('iiissd', $userSupplierId, $featuredPage, $spot, $now, $expiry, $pricePaid);

    if ($stmt->execute()) {
        $stmt->close();
        $mysqli->commit();
        $mysqli->autocommit(TRUE);
        $mysqli->close();
        // Success - return appropriate response
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            if (ob_get_level()) ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'redirect' => "router.php?action=add-product&featured_page=" . $featuredPage . "&spot=" . $spot
            ]);
            exit;
        }
        // Redirect to add product page
        $redirectUrl = "router.php?action=add-product&featured_page=" . $featuredPage . "&spot=" . $spot;
        header('Location: ' . $redirectUrl);
        exit;
    } else {
        $stmt->close();
        throw new Exception('Failed to purchase spot: ' . $stmt->error);
    }

} catch (Exception $e) {
    // Rollback transaction if it was started
    if (isset($mysqli)) {
        $mysqli->rollback();
        $mysqli->autocommit(TRUE);
    }
    
    error_log("Error purchasing spot: " . $e->getMessage());
    
    if (isset($mysqli)) {
        $mysqli->close();
    }
    
    // Return appropriate error response
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $e->getMessage() ?: 'An error occurred while purchasing the spot. Please try again later.']);
        exit;
    }
    
    header('Location: router.php?featured_page=' . $featuredPage . '&error=purchase_failed');
    exit;
}
?>