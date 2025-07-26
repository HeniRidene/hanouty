<?php
ob_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/hanouty/controller/FrontController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// --- Cart Helper Functions ---
function getCart() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    return $_SESSION['cart'];
}
function saveCart($cart) {
    $_SESSION['cart'] = $cart;
    // If user is logged in, save to DB
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $mysqli = new mysqli('localhost', 'root', '', 'hanouty');
        $cartJson = json_encode($cart);
        $stmt = $mysqli->prepare('INSERT INTO carts (user_id, cart_data, updated_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE cart_data = VALUES(cart_data), updated_at = NOW()');
        $stmt->bind_param('is', $userId, $cartJson);
        $stmt->execute();
        $stmt->close();
    }
}
function loadCartFromDb() {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $mysqli = new mysqli('localhost', 'root', '', 'hanouty');
        $stmt = $mysqli->prepare('SELECT cart_data FROM carts WHERE user_id = ?');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->bind_result($cartJson);
        if ($stmt->fetch() && $cartJson) {
            $_SESSION['cart'] = json_decode($cartJson, true) ?: [];
        }
        $stmt->close();
    }
}
// Always load cart from DB for logged-in users
if (isset($_SESSION['user_id'])) {
    loadCartFromDb();
}

$controller = new FrontController();

// Get the action from URL parameter
$action = $_GET['action'] ?? 'index';

// Handle different actions
switch ($action) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $controller->login($_POST['email'] ?? '', $_POST['password'] ?? '');
            $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            if (isset($result['redirect'])) {
                // Merge session cart with DB cart
                $userId = $_SESSION['user_id'];
                $sessionCart = $_SESSION['cart'] ?? [];
                $dbCart = [];
                $mysqli = new mysqli('localhost', 'root', '', 'hanouty');
                $stmt = $mysqli->prepare('SELECT cart_data FROM carts WHERE user_id = ?');
                $stmt->bind_param('i', $userId);
                $stmt->execute();
                $stmt->bind_result($cartJson);
                if ($stmt->fetch() && $cartJson) {
                    $dbCart = json_decode($cartJson, true) ?: [];
                }
                $stmt->close();
                foreach ($sessionCart as $pid => $qty) {
                    if (isset($dbCart[$pid])) {
                        $dbCart[$pid] += $qty;
                    } else {
                        $dbCart[$pid] = $qty;
                    }
                }
                saveCart($dbCart);
                if ($isAjax) {
                    ob_end_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                    exit;
                } else {
                    header('Location: ' . $result['redirect']);
                    exit;
                }
            } else {
                $loginError = $result['error'];
                if ($isAjax) {
                    ob_end_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $loginError]);
                    exit;
                }
            }
        }
        include 'views/login.php';
        break;
        
    case 'logout':
        $result = $controller->logout();
        header('Location: ' . $result['redirect']);
        exit;
        break;
        
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $controller->register($_POST);
            if (isset($result['success'])) {
                $registrationSuccess = $result['success'];
            } else {
                $registrationError = $result['error'];
            }
        }
        include 'views/register.php';
        break;
        
    case 'supplier':
        $supplierId = $_GET['id'] ?? null;
        if ($supplierId) {
            $data = $controller->getSupplier($supplierId);
            if ($data) {
                $supplier = $data['supplier'];
                $products = $data['products'];
                include 'views/supplier.php';
            } else {
                header('Location: index.php');
                exit;
            }
        } else {
            header('Location: index.php');
            exit;
        }
        break;
        
    case 'product':
        $productId = $_GET['id'] ?? null;
        if ($productId) {
            $product = $controller->getProduct($productId);
            if ($product) {
                include 'views/product.php';
            } else {
                header('Location: index.php');
                exit;
            }
        } else {
            header('Location: index.php');
            exit;
        }
        break;
        
    case 'add-product':
        // Check if user is logged in and is a supplier
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
            header('Location: router.php');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $controller->addProduct($_POST, $_FILES);
            if (isset($result['success'])) {
                $addProductSuccess = $result['success'];
                // Assign product to featured spot if info is present
                if (!empty($_POST['featured_page']) && !empty($_POST['spot'])) {
                    // Get the last inserted product ID for this supplier
                    $mysqli = new mysqli('localhost', 'root', '', 'hanouty');
                    $supplierId = $_SESSION['user_id'];
                    $productRes = $mysqli->query("SELECT id FROM products WHERE user_id = '" . $supplierId . "' ORDER BY created_at DESC, id DESC LIMIT 1");
                    if ($productRow = $productRes->fetch_assoc()) {
                        $productId = $productRow['id'];
                        $featuredPage = (int)$_POST['featured_page'];
                        $spot = (int)$_POST['spot'];
                        // Update featured_spots table
                        $updateStmt = $mysqli->prepare('UPDATE featured_spots SET product_id = ? WHERE page_number = ? AND spot_number = ? AND supplier_id = ?');
                        $updateStmt->bind_param('iiii', $productId, $featuredPage, $spot, $supplierId);
                        $updateStmt->execute();
                        $updateStmt->close();
                    }
                    // Redirect to the featured page after adding product
                    header('Location: router.php?featured_page=' . urlencode($featuredPage));
                    exit;
                }
            } else {
                $addProductError = $result['error'];
            }
        }
        include 'views/add-product.php';
        break;
        
    case 'edit-product':
        // Only suppliers can edit their own products
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
            header('Location: router.php');
            exit;
        }
        $productId = $_GET['id'] ?? null;
        $featuredPage = $_GET['featured_page'] ?? 1;
        $spot = $_GET['spot'] ?? null;
        if (!$productId) {
            header('Location: router.php');
            exit;
        }
        $product = $controller->getProduct($productId);
        if (!$product || $product['user_id'] != $_SESSION['user_id']) {
            header('Location: router.php');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $controller->updateProduct($productId, $_POST, $_FILES);
            if (isset($result['success'])) {
                // Redirect back to the featured page/spot
                header('Location: router.php?featured_page=' . urlencode($featuredPage));
                exit;
            } else {
                $editProductError = $result['error'];
            }
        }
        include 'views/edit-product.php';
        break;
        
    case 'profile':
        if (!isset($_SESSION['user_id'])) {
            header('Location: router.php');
            exit;
        }
        $user = [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? '',
            'email' => $_SESSION['user_email'] ?? '',
            'role' => $_SESSION['user_role'] ?? '',
        ];
        include 'views/profile.php';
        break;
        
    case 'common-products':
        // DB connection
        $mysqli = new mysqli('localhost', 'root', '', 'hanouty');
        if ($mysqli->connect_errno) {
            die('Database connection failed: ' . $mysqli->connect_error);
        }
        $now = date('Y-m-d H:i:s');
        // Get all product IDs currently in an active spot
        $activeSpotProductIds = [];
        $res = $mysqli->query("SELECT product_id FROM featured_spots WHERE end_date > '$now' AND product_id IS NOT NULL");
        while ($row = $res->fetch_assoc()) {
            $activeSpotProductIds[] = (int)$row['product_id'];
        }
        // Get all products not in an active spot, join users for supplier name
        $idsStr = $activeSpotProductIds ? implode(',', $activeSpotProductIds) : '0';
        $products = [];
        $sql = "SELECT p.*, u.name AS supplier_name FROM products p LEFT JOIN users u ON p.user_id = u.id WHERE p.id NOT IN ($idsStr) AND p.is_flash_sale = 0 ORDER BY p.created_at DESC";
        $res = $mysqli->query($sql);
        while ($row = $res->fetch_assoc()) {
            $products[] = $row;
        }
        include 'views/common-products.php';
        break;
        
    case 'flash-sale':
        // Fetch flash sale products (assuming products have an 'is_flash_sale' flag or join with a flash_sales table)
        $mysqli = new mysqli('localhost', 'root', '', 'hanouty');
        $flashProducts = [];
        $res = $mysqli->query("SELECT * FROM products WHERE is_flash_sale = 1 ORDER BY created_at DESC");
        while ($row = $res->fetch_assoc()) {
            $flashProducts[] = $row;
        }
        include 'views/flash-sale.php';
        break;
        
    case 'add-flash-sale-product':
        // Only suppliers can add flash sale products
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
            header('Location: router.php');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Force is_flash_sale to 1
            $_POST['is_flash_sale'] = 1;
            $result = $controller->addProduct($_POST, $_FILES);
            if (isset($result['success'])) {
                header('Location: router.php?action=flash-sale');
                exit;
            } else {
                $addProductError = $result['error'];
            }
        }
        include 'views/add-flash-sale-product.php';
        break;
        
    case 'about-us':
        include 'views/about-us.php';
        break;
        
    case 'delete-product':
        // Only suppliers can delete their own products
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
            header('Location: router.php');
            exit;
        }
        $productId = $_GET['id'] ?? null;
        $featuredPage = $_GET['featured_page'] ?? 1;
        $spot = $_GET['spot'] ?? null;
        if (!$productId) {
            header('Location: router.php');
            exit;
        }
        // Get product and check ownership
        $product = $controller->getProduct($productId);
        if (!$product || $product['user_id'] != $_SESSION['user_id']) {
            header('Location: router.php');
            exit;
        }
        // Remove product from featured spot
        $mysqli = new mysqli('localhost', 'root', '', 'hanouty');
        $updateStmt = $mysqli->prepare('UPDATE featured_spots SET product_id = NULL WHERE page_number = ? AND spot_number = ? AND product_id = ?');
        $updateStmt->bind_param('iii', $featuredPage, $spot, $productId);
        $updateStmt->execute();
        $updateStmt->close();
        // Delete product from products table
        $controller->deleteProduct($productId);
        // Redirect back to the featured page
        header('Location: router.php?featured_page=' . urlencode($featuredPage));
        exit;
        
    case 'add-to-cart':
        if (!isset($_SESSION['user_id'])) {
            // Redirect guests to login page with redirect info
            $productId = isset($_GET['id']) ? urlencode($_GET['id']) : '';
            $quantity = isset($_POST['quantity']) ? urlencode($_POST['quantity']) : 1;
            header('Location: router.php?action=login&redirect=add-to-cart&id=' . $productId . '&quantity=' . $quantity);
            exit;
        } else {
            // For logged-in users, add to DB cart
            $productId = (int)($_GET['id'] ?? 0);
            $quantity = 1; // Always add 1 per click, regardless of form
            $cart = getCart(); // Get current cart from session
            if (isset($cart[$productId])) {
                $cart[$productId] += $quantity;
            } else {
                $cart[$productId] = $quantity;
            }
            saveCart($cart); // Save updated cart to DB
            // If AJAX, return JSON, else do nothing (stay on page)
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            }
            // No redirect, just exit to stay on the same page
            exit;
        }
        
    case 'remove-from-cart':
        if (!isset($_SESSION['user_id'])) {
            // For guests, remove from session cart
            $productId = (int)$_GET['id'];
            $cart = getCart();
            unset($cart[$productId]);
            saveCart($cart);
        } else {
            // For logged-in users, remove from DB cart
            $productId = (int)$_GET['id'];
            $cart = getCart(); // Get current cart from session
            unset($cart[$productId]);
            saveCart($cart); // Save updated cart to DB
        }
        header('Location: router.php?action=cart');
        exit;
        
    case 'cart':
        if (!isset($_SESSION['user_id'])) {
            header('Location: router.php?action=login&redirect=cart');
            exit;
        }
        $cart = getCart();
        $products = [];
        $mysqli = new mysqli('localhost', 'root', '', 'hanouty');
        $ids = array_keys($cart);
        if ($ids) {
            $idsStr = implode(',', array_map('intval', $ids));
            $res = $mysqli->query("SELECT * FROM products WHERE id IN ($idsStr)");
            while ($row = $res->fetch_assoc()) {
                $row['quantity'] = $cart[$row['id']];
                $products[] = $row;
            }
        }
        $deliveryFee = 5;
        include 'views/cart.php';
        exit;
        
    case 'cart-count':
        $cart = getCart();
        $count = array_sum($cart);
        header('Content-Type: application/json');
        echo json_encode(['count' => $count]);
        exit;
        
    default:
        // --- DB CONNECTION ---
        $mysqli = new mysqli('localhost', 'root', '', 'hanouty');
        if ($mysqli->connect_errno) {
            die('Database connection failed: ' . $mysqli->connect_error);
        }

        // --- Prepare variables for the view ---
        $userRole = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'supplier') ? 'supplier' : null;
        $supplierId = $_SESSION['user_id'] ?? null;
        $featuredPage = isset($_GET['featured_page']) ? (int)$_GET['featured_page'] : 1;
        
        // Initialize spot prices with defaults
        $spotPrices = [1=>100,2=>90,3=>80,4=>70,5=>60,6=>50,7=>40,8=>30,9=>20,10=>10];
        // Get system supplier first for default prices
        $stmt = $mysqli->query("SELECT s.user_id FROM supplier s JOIN users u ON s.user_id = u.id WHERE s.business_name = 'System Default'");
        if ($systemSupplier = $stmt->fetch_assoc()) {
            $systemSupplierId = $systemSupplier['user_id'];
            // Now get prices using system supplier ID
            $spotPriceResult = $mysqli->prepare('SELECT spot_number, price_paid FROM featured_spots WHERE page_number = ? AND supplier_id = ?');
            $spotPriceResult->bind_param('ii', $featuredPage, $systemSupplierId);
            $spotPriceResult->execute();
            $result = $spotPriceResult->get_result();
            while ($row = $result->fetch_assoc()) {
                $spotPrices[(int)$row['spot_number']] = (int)$row['price_paid'];
            }
            $spotPriceResult->close();
        }

        // --- Pagination: count total pages ---
        $res = $mysqli->query('SELECT MAX(page_number) as max_page FROM featured_spots');
        $row = $res->fetch_assoc();
        $minPages = 3;
        $totalPages = max($minPages, (int)($row['max_page'] ?? 1));

        // --- Fetch all spots for this page ---
        $spots = [];
        $now = date('Y-m-d H:i:s');
        
        // First get all spots that are actually purchased (non-system supplier)
        $stmt = $mysqli->prepare('SELECT fs.spot_number, fs.supplier_id, fs.product_id, fs.end_date, p.title, p.description, p.price, p.images 
            FROM featured_spots fs 
            LEFT JOIN products p ON fs.product_id = p.id 
            WHERE fs.page_number = ? 
            AND fs.supplier_id != ? 
            AND fs.product_id IS NOT NULL
            AND fs.end_date > ?
            ORDER BY fs.spot_number, fs.end_date DESC');
        $stmt->bind_param('iis', $featuredPage, $systemSupplierId, $now);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $spotNum = (int)$row['spot_number'];
            $spots[$spotNum] = $row;
        }
        $stmt->close();

        // For spots without a real purchase, add the system price
        for ($i = 1; $i <= 10; $i++) {
            if (!isset($spots[$i])) {
                $spots[$i] = [
                    'spot_number' => $i,
                    'supplier_id' => null,
                    'product_id' => null,
                    'end_date' => null,
                    'title' => null,
                    'description' => null,
                    'price' => null,
                    'images' => null,
                    'price_paid' => $spotPrices[$i] ?? null
                ];
            }
        }
        
        // --- Also fetch regular suppliers for the main section ---
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $suppliersData = $controller->getSuppliers($page, 10);
        $suppliers = $suppliersData['suppliers'];
        $pagination = $suppliersData['pagination'];
        $searchTerm = $_GET['search'] ?? '';
        $searchResults = [];
        
        if ($searchTerm) {
            $searchResults = $controller->searchProducts($searchTerm);
        }
        
        // --- Now, include the view, which has access to all these variables ---
        include 'views/index.php';
        break;
}
?> 
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.addEventListener('submit', function(e) {
        if (e.target && e.target.matches('form[id^="buy-form-"]')) {
            e.preventDefault();
            var form = e.target;
            var formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count
                    fetch('router.php?action=cart-count')
                        .then(res => res.json())
                        .then(data => {
                            var cartCount = document.getElementById('cart-count');
                            if (cartCount) cartCount.textContent = data.count;
                        });
                    if (typeof bootstrap !== 'undefined' && document.getElementById('cartToast')) {
                        var toast = new bootstrap.Toast(document.getElementById('cartToast'));
                        toast.show();
                    }
                }
            });
        }
    });
});
</script> 