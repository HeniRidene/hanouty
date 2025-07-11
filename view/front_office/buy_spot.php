<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
    header('Location: index.php');
    exit;
}

$mysqli = new mysqli('localhost', 'root', '', 'hanouty');
if ($mysqli->connect_errno) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

$supplierId = $_SESSION['user_id'];
$featuredPage = isset($_GET['featured_page']) ? (int)$_GET['featured_page'] : 1;
$spot = isset($_GET['spot']) ? (int)$_GET['spot'] : 0;
$spotPrices = [1=>100,2=>90,3=>80,4=>70,5=>60,6=>50,7=>40,8=>30,9=>20,10=>10];

// Validate spot
if ($spot < 1 || $spot > 10) {
    echo 'invalid_spot';
    exit;
}

// Ensure supplier exists in supplier table
$stmt = $mysqli->prepare('SELECT id FROM supplier WHERE user_id = ?');
$stmt->bind_param('i', $supplierId);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    // Insert supplier record if not exists (minimal info)
    $stmt->close();
    $stmt2 = $mysqli->prepare('INSERT INTO supplier (user_id, business_name, bio) VALUES (?, ?, ?)');
    $empty = '';
    $stmt2->bind_param('iss', $supplierId, $empty, $empty);
    $stmt2->execute();
    $stmt2->close();
} else {
    $stmt->close();
}

// Check if spot is already taken (not expired)
$stmt = $mysqli->prepare('SELECT end_date FROM featured_spots WHERE page_number = ? AND spot_number = ? ORDER BY end_date DESC LIMIT 1');
$stmt->bind_param('ii', $featuredPage, $spot);
$stmt->execute();
$stmt->bind_result($endDate);
if ($stmt->fetch()) {
    if (!empty($endDate) && $endDate > date('Y-m-d H:i:s')) {
        $stmt->close();
        echo 'spot_taken';
        exit;
    }
}
$stmt->close();

// Insert new spot ownership
$now = date('Y-m-d H:i:s');
$expiry = date('Y-m-d H:i:s', strtotime('3 days'));
$pricePaid = $spotPrices[$spot] ?? 0;
$stmt = $mysqli->prepare('INSERT INTO featured_spots (supplier_id, page_number, spot_number, start_date, end_date, price_paid) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->bind_param('iiissd', $supplierId, $featuredPage, $spot, $now, $expiry, $pricePaid);
$stmt->execute();
$stmt->close();

echo 'success';
exit;