<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/hanouty/controller/FrontController.php';

$controller = new FrontController();

// Get the action from URL parameter
$action = $_GET['action'] ?? 'index';

// Handle different actions
switch ($action) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $controller->login($_POST['email'] ?? '', $_POST['password'] ?? '');
            if (isset($result['redirect'])) {
                header('Location: ' . $result['redirect']);
                exit;
            } else {
                $loginError = $result['error'];
            }
        }
        include 'views/index.php';
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
        $spotPrices = [1=>100,2=>90,3=>80,4=>70,5=>60,6=>50,7=>40,8=>30,9=>20,10=>10];

        // --- Pagination: count total pages ---
        $res = $mysqli->query('SELECT MAX(page_number) as max_page FROM featured_spots');
        $row = $res->fetch_assoc();
        $minPages = 3;
        $totalPages = max($minPages, (int)($row['max_page'] ?? 1));

        // --- Fetch all spots for this page ---
        $spots = [];
        $stmt = $mysqli->prepare('SELECT fs.spot_number, fs.supplier_id, fs.product_id, p.title, p.description, p.price, p.images FROM featured_spots fs LEFT JOIN products p ON fs.product_id = p.id WHERE fs.page_number = ?');
        $stmt->bind_param('i', $featuredPage);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $spots[(int)$row['spot_number']] = $row;
        }
        $stmt->close();
        
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