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
            } else {
                $addProductError = $result['error'];
            }
        }
        include 'views/add-product.php';
        break;
        
    default:
        // Default action - show home page
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $suppliersData = $controller->getSuppliers($page, 10);
        $suppliers = $suppliersData['suppliers'];
        $pagination = $suppliersData['pagination'];
        $searchTerm = $_GET['search'] ?? '';
        $searchResults = [];
        
        if ($searchTerm) {
            $searchResults = $controller->searchProducts($searchTerm);
        }
        
        include 'views/index.php';
        break;
}
?> 