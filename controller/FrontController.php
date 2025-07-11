<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/hanouty/model/User.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/hanouty/model/Product.php';

class FrontController {
    private $userModel;
    private $productModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->productModel = new Product();
    }
    
    // Handle login
    public function login($email, $password) {
        try {
            $user = $this->userModel->authenticate($email, $password);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    return ['redirect' => '../back_office/index.php'];
                } elseif ($user['role'] === 'supplier' || $user['role'] === 'client') {
                    return ['redirect' => '/hanouty/view/front_office/router.php'];
                } else {
                    return ['redirect' => '/hanouty/view/front_office/router.php'];
                }
            } else {
                return ['error' => 'Invalid email or password'];
            }
        } catch (Exception $e) {
            return ['error' => 'Login error: ' . $e->getMessage()];
        }
    }
    
    // Handle logout
    public function logout() {
        session_destroy();
        return ['redirect' => '/hanouty/view/front_office/router.php'];
    }
    
    // Handle registration
    public function register($data) {
        // Validation
        if (empty($data['name']) || empty($data['email']) || empty($data['password']) || empty($data['role'])) {
            return ['error' => 'Please fill in all required fields.'];
        }
        
        if ($data['password'] !== $data['confirm_password']) {
            return ['error' => 'Passwords do not match.'];
        }
        
        if (strlen($data['password']) < 6) {
            return ['error' => 'Password must be at least 6 characters long.'];
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Please enter a valid email address.'];
        }
        
        try {
            // Check if email already exists
            if ($this->userModel->emailExists($data['email'])) {
                return ['error' => 'Email address already exists.'];
            }
            
            // Prepare user data
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => $data['role']
            ];
            
            // Add role-specific data
            if ($data['role'] === 'supplier') {
                $userData['business_name'] = $data['business_name'] ?? '';
                $userData['bio'] = $data['bio'] ?? '';
            } elseif ($data['role'] === 'client') {
                $userData['address'] = $data['address'] ?? '';
                $userData['phone'] = $data['phone'] ?? '';
            }
            
            // Create user
            if ($this->userModel->create($userData)) {
                return ['success' => 'Registration successful! You can now login.'];
            } else {
                return ['error' => 'Registration failed. Please try again.'];
            }
        } catch (Exception $e) {
            return ['error' => 'Registration error: ' . $e->getMessage()];
        }
    }
    
    // Get suppliers with products (paginated)
    public function getSuppliers($page = 1, $perPage = 10) {
        try {
            $allSuppliers = $this->productModel->getSuppliersWithProducts();
            
            // Filter suppliers with products
            $suppliersWithProducts = array_filter($allSuppliers, function($supplier) {
                return $supplier['products_count'] > 0;
            });
            
            // Calculate pagination
            $totalSuppliers = count($suppliersWithProducts);
            $totalPages = ceil($totalSuppliers / $perPage);
            $offset = ($page - 1) * $perPage;
            
            // Get suppliers for current page
            $suppliers = array_slice($suppliersWithProducts, $offset, $perPage);
            
            return [
                'suppliers' => $suppliers,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_suppliers' => $totalSuppliers,
                    'per_page' => $perPage
                ]
            ];
        } catch (Exception $e) {
            return [
                'suppliers' => [],
                'pagination' => [
                    'current_page' => 1,
                    'total_pages' => 1,
                    'total_suppliers' => 0,
                    'per_page' => $perPage
                ]
            ];
        }
    }
    
    // Search products
    public function searchProducts($searchTerm) {
        try {
            return $this->productModel->searchProducts($searchTerm);
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get supplier details
    public function getSupplier($supplierId) {
        try {
            $supplier = $this->userModel->getUserWithDetails($supplierId);
            if ($supplier && $supplier['role'] === 'supplier') {
                $products = $this->productModel->getProductsBySupplier($supplierId);
                return ['supplier' => $supplier, 'products' => $products];
            }
            return null;
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Get product details
    public function getProduct($productId) {
        try {
            $product = $this->productModel->getById($productId);
            if ($product && $product['status'] === 'active') {
                return $product;
            }
            return null;
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Get supplier products
    public function getSupplierProducts($supplierId) {
        try {
            return $this->productModel->getProductsBySupplier($supplierId);
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Add new product
    public function addProduct($data, $files) {
        try {
            // Validate required fields
            if (empty($data['title']) || empty($data['description']) || empty($data['price'])) {
                return ['error' => 'Please fill in all required fields.'];
            }
            
            // Validate price
            if (!is_numeric($data['price']) || $data['price'] <= 0) {
                return ['error' => 'Please enter a valid price.'];
            }
            
            // Handle image uploads
            $uploadedImages = [];
            if (!empty($files['images']['name'][0])) {
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/hanouty/uploads/products/';
                
                // Create directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                foreach ($files['images']['tmp_name'] as $key => $tmpName) {
                    if ($files['images']['error'][$key] === UPLOAD_ERR_OK) {
                        $fileName = $files['images']['name'][$key];
                        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        
                        // Validate file type
                        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        if (!in_array($fileExtension, $allowedTypes)) {
                            return ['error' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.'];
                        }
                        
                        // Generate unique filename
                        $uniqueName = uniqid() . '_' . time() . '.' . $fileExtension;
                        $filePath = $uploadDir . $uniqueName;
                        
                        if (move_uploaded_file($tmpName, $filePath)) {
                            $uploadedImages[] = '/hanouty/uploads/products/' . $uniqueName;
                        }
                    }
                }
            }
            
            // Prepare product data
            $productData = [
                'title' => trim($data['title']),
                'description' => trim($data['description']),
                'price' => (float)$data['price'],
                'category' => trim($data['category'] ?? ''),
                'images' => !empty($uploadedImages) ? json_encode($uploadedImages) : null,
                'supplier_id' => $_SESSION['user_id']
            ];
            
            // Add product to database
            $result = $this->productModel->addProduct($productData);
            
            if ($result) {
                return ['success' => 'Product added successfully!'];
            } else {
                return ['error' => 'Failed to add product. Please try again.'];
            }
            
        } catch (Exception $e) {
            // Log the error for debugging
            error_log("Product addition error: " . $e->getMessage());
            return ['error' => 'An error occurred while adding the product: ' . $e->getMessage()];
        }
    }
    
    // Update product
    public function updateProduct($productId, $data, $files) {
        try {
            // Validate required fields
            if (empty($data['title']) || empty($data['description']) || empty($data['price'])) {
                return ['error' => 'Please fill in all required fields.'];
            }
            if (!is_numeric($data['price']) || $data['price'] <= 0) {
                return ['error' => 'Please enter a valid price.'];
            }
            // Handle image uploads (optional, can replace or keep old images)
            $uploadedImages = [];
            if (!empty($files['images']['name'][0])) {
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/hanouty/uploads/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                foreach ($files['images']['tmp_name'] as $key => $tmpName) {
                    if ($files['images']['error'][$key] === UPLOAD_ERR_OK) {
                        $fileName = $files['images']['name'][$key];
                        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        if (!in_array($fileExtension, $allowedTypes)) {
                            return ['error' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.'];
                        }
                        $uniqueName = uniqid() . '_' . time() . '.' . $fileExtension;
                        $filePath = $uploadDir . $uniqueName;
                        if (move_uploaded_file($tmpName, $filePath)) {
                            $uploadedImages[] = '/hanouty/uploads/products/' . $uniqueName;
                        }
                    }
                }
            }
            // Prepare update data
            $updateData = [
                'title' => trim($data['title']),
                'description' => trim($data['description']),
                'price' => (float)$data['price'],
                'category' => trim($data['category'] ?? ''),
                'images' => !empty($uploadedImages) ? json_encode($uploadedImages) : null
            ];
            $result = $this->productModel->updateProduct($productId, $updateData);
            if ($result) {
                return ['success' => 'Product updated successfully!'];
            } else {
                return ['error' => 'Failed to update product. Please try again.'];
            }
        } catch (Exception $e) {
            return ['error' => 'An error occurred while updating the product: ' . $e->getMessage()];
        }
    }
}
?> 